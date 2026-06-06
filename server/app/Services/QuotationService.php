<?php

namespace App\Services;

use App\Models\Rfq;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Enums\Procurement\QuotationStatusEnum;
use App\Enums\Procurement\RfqVendorInvitationStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CustomException;

class QuotationService
{
    /**
     * Submit a quotation by a Vendor.
     */
    public function submit(int $rfqId, array $inputs): Quotation
    {
        $user = auth('api')->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            throw new CustomException("User is not registered as a Vendor.");
        }

        $rfq = Rfq::findOrFail($rfqId);

        // Check if RFQ is open for submissions
        if ($rfq->status->value !== \App\Enums\Procurement\RfqStatusEnum::OPEN->value) {
            throw new CustomException("This RFQ is not open for quotation submission.");
        }

        // Verify vendor was invited to this RFQ
        $invitation = DB::table('rfq_vendor')
            ->where('rfq_id', $rfqId)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$invitation) {
            throw new CustomException("You are not invited to submit a quotation for this RFQ.");
        }

        return DB::transaction(function () use ($rfqId, $vendor, $inputs) {
            $year = now()->year;
            $latestQuote = Quotation::latest('id')->first();
            $nextId = $latestQuote ? $latestQuote->id + 1 : 1;
            $quoteNumber = 'QTN-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Create Quotation Header (totals updated after items insertion)
            $quotation = Quotation::create([
                'rfq_id' => $rfqId,
                'vendor_id' => $vendor->id,
                'quotation_number' => $quoteNumber,
                'delivery_days' => $inputs['delivery_days'] ?? null,
                'notes' => $inputs['notes'] ?? null,
                'status' => QuotationStatusEnum::SUBMITTED->value,
                'submitted_at' => now(),
            ]);

            $subtotal = 0;
            $taxAmount = 0;
            $totalAmount = 0;

            if (isset($inputs['items']) && is_array($inputs['items'])) {
                foreach ($inputs['items'] as $item) {
                    $itemQty = $item['quantity'];
                    $itemPrice = $item['unit_price'];
                    $taxPct = $item['tax_percent'] ?? 0;

                    $lineSubtotal = $itemQty * $itemPrice;
                    $lineTax = $lineSubtotal * ($taxPct / 100);
                    $lineTotal = $lineSubtotal + $lineTax;

                    $subtotal += $lineSubtotal;
                    $taxAmount += $lineTax;
                    $totalAmount += $lineTotal;

                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'rfq_item_id' => $item['rfq_item_id'],
                        'unit_price' => $itemPrice,
                        'quantity' => $itemQty,
                        'tax_percent' => $taxPct,
                        'line_total' => $lineTotal,
                    ]);
                }
            }

            // Update header totals
            $quotation->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            // Update RFQ Vendor Invitation Status to Submitted
            DB::table('rfq_vendor')
                ->where('rfq_id', $rfqId)
                ->where('vendor_id', $vendor->id)
                ->update([
                    'invitation_status' => RfqVendorInvitationStatusEnum::SUBMITTED->value,
                    'responded_at' => now(),
                ]);

            // Log activity
            ActivityLogService::log(
                'Quotation',
                Quotation::class,
                $quotation->id,
                'submitted',
                "Vendor {$vendor->company_name} submitted quotation #{$quotation->quotation_number}."
            );

            return $quotation->load('items');
        });
    }

    /**
     * Compare all quotations for an RFQ side-by-side.
     */
    public function compare(int $rfqId): array
    {
        $rfq = Rfq::with(['items'])->findOrFail($rfqId);
        $quotations = Quotation::with(['vendor', 'items.rfqItem'])
            ->where('rfq_id', $rfqId)
            ->where('status', QuotationStatusEnum::SUBMITTED->value)
            ->get();

        if ($quotations->isEmpty()) {
            return [
                'rfq' => $rfq,
                'comparison' => [],
            ];
        }

        // Find lowest price and fastest delivery
        $minPrice = $quotations->min('total_amount');
        $minDelivery = $quotations->min('delivery_days');

        $comparison = $quotations->map(function ($q) use ($minPrice, $minDelivery) {
            return [
                'quotation_id' => $q->id,
                'quotation_number' => $q->quotation_number,
                'vendor_id' => $q->vendor_id,
                'vendor_name' => $q->vendor->company_name,
                'category' => $q->vendor->category,
                'delivery_days' => $q->delivery_days,
                'subtotal' => $q->subtotal,
                'tax_amount' => $q->tax_amount,
                'total_amount' => $q->total_amount,
                'notes' => $q->notes,
                'is_lowest_price' => $q->total_amount == $minPrice,
                'is_fastest_delivery' => $q->delivery_days == $minDelivery,
                'items' => $q->items->map(function ($item) {
                    return [
                        'rfq_item_id' => $item->rfq_item_id,
                        'item_name' => $item->rfqItem->item_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'tax_percent' => $item->tax_percent,
                        'line_total' => $item->line_total,
                    ];
                }),
            ];
        })->toArray();

        return [
            'rfq' => $rfq,
            'comparison' => $comparison,
        ];
    }
}
