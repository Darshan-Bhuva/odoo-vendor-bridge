<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;

#[Middleware(['auth:api'])]
class InvoiceController extends Controller
{
    use ApiResponser;

    private InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * List Invoices (Procurement Officer / Admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $invoices = $this->invoiceService->list($request->only(['status', 'search']));
        return $this->success($invoices);
    }

    /**
     * Generate Invoice from Purchase Order (Procurement Officer / Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'purchase_order_id' => 'required|integer|exists:purchase_orders,id',
        ]);

        $invoice = $this->invoiceService->generate($request->input('purchase_order_id'));

        return $this->success([
            'success' => true,
            'message' => "Invoice #{$invoice->invoice_number} generated successfully.",
            'invoice' => $invoice
        ], 201);
    }

    /**
     * Show Invoice details.
     */
    public function show(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->get($id);
        return $this->success($invoice);
    }

    /**
     * Send Invoice PDF via Email (Procurement Officer / Admin only).
     */
    public function sendEmail(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->sendEmail($id);
        return $this->success([
            'success' => true,
            'message' => 'Invoice PDF successfully sent to client\'s registered email.',
            'invoice' => $invoice
        ]);
    }

    /**
     * Mock Invoice PDF Download.
     */
    public function download(int $id)
    {
        $invoice = $this->invoiceService->get($id);
        
        $html = "
            <h1>Invoice #{$invoice->invoice_number}</h1>
            <p>Date: {$invoice->invoice_date->toDateString()}</p>
            <p>Vendor: {$invoice->vendor->company_name}</p>
            <hr>
            <h3>Items:</h3>
            <ul>
        ";
        foreach ($invoice->items as $item) {
            $html .= "<li>{$item->item_name} - Qty: {$item->quantity} - Price: {$item->unit_price} - Line Total: {$item->line_total}</li>";
        }
        $html .= "</ul><hr><p>Subtotal: {$invoice->subtotal}</p><p>Tax: {$invoice->tax_amount}</p><h3>Grand Total: {$invoice->total_amount}</h3>";

        return response($html, 200)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "attachment; filename=\"invoice-{$invoice->invoice_number}.html\"");
    }
}
