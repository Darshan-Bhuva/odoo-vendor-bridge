<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Enums\Procurement\InvoiceStatusEnum;
use App\Enums\Procurement\PurchaseOrderStatusEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class InvoiceService
{
    /**
     * Generate an Invoice from a Purchase Order (Procurement Officer).
     */
    public function generate(int $poId): Invoice
    {
        $po = PurchaseOrder::findOrFail($poId);

        // Verify PO is generated or sent
        if (!in_array($po->status->value, [PurchaseOrderStatusEnum::GENERATED->value, PurchaseOrderStatusEnum::SENT->value])) {
            throw new CustomException("This Purchase Order is not ready for invoice generation.");
        }

        // Check if invoice already generated
        $existingInvoice = Invoice::where('purchase_order_id', $poId)->first();
        if ($existingInvoice) {
            throw new CustomException("An Invoice has already been generated for this Purchase Order.");
        }

        return DB::transaction(function () use ($po) {
            $year = now()->year;
            $latestInvoice = Invoice::latest('id')->first();
            $nextId = $latestInvoice ? $latestInvoice->id + 1 : 1;
            $invoiceNumber = 'INV-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'purchase_order_id' => $po->id,
                'vendor_id' => $po->vendor_id,
                'created_by' => auth('api')->id(),
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now()->toDateString(),
                'subtotal' => $po->subtotal,
                'tax_amount' => $po->tax_amount,
                'total_amount' => $po->total_amount,
                'status' => InvoiceStatusEnum::GENERATED->value,
            ]);

            // Copy items
            foreach ($po->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'purchase_order_item_id' => $item->id,
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax_percent' => $item->tax_percent,
                    'line_total' => $item->line_total,
                ]);
            }

            // Log activity
            ActivityLogService::log(
                'Invoice',
                Invoice::class,
                $invoice->id,
                'generated',
                "Invoice #{$invoice->invoice_number} generated for Purchase Order #{$po->po_number}."
            );

            return $invoice->load('items');
        });
    }

    /**
     * List Invoices.
     */
    public function list(array $filters = [])
    {
        $user = auth('api')->user();
        $query = Invoice::with(['vendor', 'purchaseOrder', 'creator']);

        // Vendors are restricted from viewing general invoices unless specifically linked
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
                $q->where('invoice_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(config('site.pagination_limit', 10));
    }

    /**
     * Get Invoice details.
     */
    public function get(int $id): Invoice
    {
        $user = auth('api')->user();
        $invoice = Invoice::with(['items', 'vendor', 'purchaseOrder', 'creator'])->findOrFail($id);

        if ($user->hasRole(config('site.roles.vendor'))) {
            $vendor = $user->vendor;
            if (!$vendor || $invoice->vendor_id !== $vendor->id) {
                throw new CustomException("Unauthorized access to this Invoice.");
            }
        }

        return $invoice;
    }

    /**
     * Send invoice via email.
     */
    public function sendEmail(int $id): Invoice
    {
        $invoice = Invoice::findOrFail($id);
        
        $invoice->update([
            'status' => InvoiceStatusEnum::EMAILED->value,
            'emailed_at' => now(),
        ]);

        ActivityLogService::log(
            'Invoice',
            Invoice::class,
            $invoice->id,
            'emailed',
            "Invoice #{$invoice->invoice_number} sent via email to vendor {$invoice->vendor->email}."
        );

        return $invoice;
    }
}
