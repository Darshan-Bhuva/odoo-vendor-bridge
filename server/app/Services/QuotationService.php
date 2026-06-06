<?php

namespace App\Services;

use App\Models\Rfq;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Enums\Procurement\QuotationStatusEnum;
use App\Enums\Procurement\RfqVendorInvitationStatusEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class QuotationService
{
    public function store(array $inputs): Quotation
    {
        $user = auth('api')->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            throw new CustomException("User is not registered as a Vendor.");
        }

        $rfq = Rfq::findOrFail($inputs['rfq_id']);

        if ($rfq->status->value !== \App\Enums\Procurement\RfqStatusEnum::OPEN->value) {
            throw new CustomException("This RFQ is not open for quotation submission.");
        }

        // Verify vendor was invited
        $invitation = DB::table('rfq_vendor')
            ->where('rfq_id', $rfq->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$invitation) {
            throw new CustomException("You are not invited to submit a quotation for this RFQ.");
        }

        // Check if draft already exists
        $existing = Quotation::where('rfq_id', $rfq->id)->where('vendor_id', $vendor->id)->first();
        if ($existing) {
            throw new CustomException("A quotation already exists for this RFQ.");
        }

        return DB::transaction(function () use ($rfq, $vendor, $inputs) {
            $year = now()->year;
            $latestQuote = Quotation::latest('id')->first();
            $nextId = $latestQuote ? $latestQuote->id + 1 : 1;
            $quoteNumber = 'QT-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'rfq_id' => $rfq->id,
                'vendor_id' => $vendor->id,
                'quotation_number' => $quoteNumber,
                'tax_percent' => $inputs['tax_percent'] ?? 0,
                'notes' => $inputs['notes'] ?? null,
                'status' => 'draft',
            ]);

            $this->syncItemsAndTotals($quotation, $inputs['items'] ?? []);

            return $quotation->load('items');
        });
    }

    public function update(int $id, array $inputs): Quotation
    {
        $quotation = Quotation::findOrFail($id);

        if ($quotation->status !== 'draft') {
            throw new CustomException("Only draft quotations can be updated.");
        }

        return DB::transaction(function () use ($quotation, $inputs) {
            $quotation->update([
                'tax_percent' => $inputs['tax_percent'] ?? $quotation->tax_percent,
                'notes' => $inputs['notes'] ?? $quotation->notes,
            ]);

            if (isset($inputs['items'])) {
                $quotation->items()->delete();
                $this->syncItemsAndTotals($quotation, $inputs['items']);
            } else {
                // Re-sync totals based on new tax
                $this->syncItemsAndTotals($quotation, $quotation->items->toArray());
            }

            return $quotation->load('items');
        });
    }

    public function submit(int $id): Quotation
    {
        $quotation = Quotation::findOrFail($id);

        if ($quotation->status !== 'draft') {
            throw new CustomException("Only draft quotations can be submitted.");
        }

        return DB::transaction(function () use ($quotation) {
            $quotation->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            DB::table('rfq_vendor')
                ->where('rfq_id', $quotation->rfq_id)
                ->where('vendor_id', $quotation->vendor_id)
                ->update([
                    'invitation_status' => RfqVendorInvitationStatusEnum::SUBMITTED->value,
                    'responded_at' => now(),
                ]);

            ActivityLogService::log(
                'Quotation',
                Quotation::class,
                $quotation->id,
                'submitted',
                "Vendor submitted quotation #{$quotation->quotation_number}."
            );

            return $quotation;
        });
    }

    private function syncItemsAndTotals(Quotation $quotation, array $items)
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $itemQty = $item['quantity'];
            $itemPrice = $item['unit_price'];
            $lineSubtotal = $itemQty * $itemPrice;

            $subtotal += $lineSubtotal;

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'rfq_item_id' => $item['rfq_item_id'],
                'unit_price' => $itemPrice,
                'quantity' => $itemQty,
                'delivery_days' => $item['delivery_days'] ?? null,
                'line_total' => $lineSubtotal,
            ]);
        }

        $taxPct = $quotation->tax_percent ?? 0;
        $taxAmount = $subtotal * ($taxPct / 100);
        $totalAmount = $subtotal + $taxAmount;

        $quotation->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }
}
