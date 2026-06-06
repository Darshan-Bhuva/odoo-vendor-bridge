<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Enums\Procurement\PurchaseOrderStatusEnum;
use App\Enums\Procurement\QuotationStatusEnum;
use App\Enums\Procurement\RfqStatusEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class PurchaseOrderService
{
    /**
     * Generate a Purchase Order from an approved quotation (Procurement Officer).
     */
    public function generate(int $quotationId): PurchaseOrder
    {
        $quotation = Quotation::with('rfq')->findOrFail($quotationId);

        // Verify quotation is selected
        if ($quotation->status->value !== QuotationStatusEnum::SELECTED->value) {
            throw new CustomException("This quotation has not been selected/approved.");
        }

        // Verify RFQ is approved
        if ($quotation->rfq->status->value !== RfqStatusEnum::APPROVED->value) {
            throw new CustomException("The associated RFQ is not approved yet.");
        }

        // Check if PO already generated
        $existingPO = PurchaseOrder::where('quotation_id', $quotationId)->first();
        if ($existingPO) {
            throw new CustomException("A Purchase Order has already been generated for this quotation.");
        }

        return DB::transaction(function () use ($quotation) {
            $year = now()->year;
            $latestPO = PurchaseOrder::latest('id')->first();
            $nextId = $latestPO ? $latestPO->id + 1 : 1;
            $poNumber = 'PO-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $po = PurchaseOrder::create([
                'rfq_id' => $quotation->rfq_id,
                'quotation_id' => $quotation->id,
                'vendor_id' => $quotation->vendor_id,
                'created_by' => auth('api')->id(),
                'po_number' => $poNumber,
                'po_date' => now()->toDateString(),
                'subtotal' => $quotation->subtotal,
                'tax_amount' => $quotation->tax_amount,
                'total_amount' => $quotation->total_amount,
                'status' => PurchaseOrderStatusEnum::GENERATED->value,
            ]);

            // Copy items
            foreach ($quotation->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'quotation_item_id' => $item->id,
                    'item_name' => $item->rfqItem->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax_percent' => $item->tax_percent,
                    'line_total' => $item->line_total,
                ]);
            }

            // Log activity
            ActivityLogService::log(
                'PurchaseOrder',
                PurchaseOrder::class,
                $po->id,
                'generated',
                "Purchase Order #{$po->po_number} generated for Vendor {$quotation->vendor->company_name}."
            );

            return $po->load('items');
        });
    }

    /**
     * List Purchase Orders.
     */
    public function list(array $filters = [])
    {
        $user = auth('api')->user();
        $query = PurchaseOrder::with(['vendor', 'rfq', 'creator']);

        // Vendor filters
        if ($user->hasRole(config('site.roles.vendor'))) {
            $vendor = $user->vendor;
            if (!$vendor) {
                return collect();
            }
            $query->where('vendor_id', $vendor->id);
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(config('site.pagination_limit', 10));
    }

    /**
     * Get PO details.
     */
    public function get(int $id): PurchaseOrder
    {
        $user = auth('api')->user();
        $po = PurchaseOrder::with(['items', 'vendor', 'rfq', 'creator'])->findOrFail($id);

        // Access check for vendor
        if ($user->hasRole(config('site.roles.vendor'))) {
            $vendor = $user->vendor;
            if (!$vendor || $po->vendor_id !== $vendor->id) {
                throw new CustomException("Unauthorized access to this Purchase Order.");
            }
        }

        return $po;
    }
}
