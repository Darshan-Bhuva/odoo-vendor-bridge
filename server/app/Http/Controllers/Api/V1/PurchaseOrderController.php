<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;

#[Middleware(['auth:api'])]
class PurchaseOrderController extends Controller
{
    use ApiResponser;

    private PurchaseOrderService $poService;

    public function __construct(PurchaseOrderService $poService)
    {
        $this->poService = $poService;
    }

    /**
     * List Purchase Orders.
     */
    public function index(Request $request): JsonResponse
    {
        $pos = $this->poService->list($request->only(['status', 'search']));
        return $this->success($pos);
    }

    /**
     * Generate PO from Approved Quotation (Procurement Officer / Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'quotation_id' => 'required|integer|exists:quotations,id',
        ]);

        $po = $this->poService->generate($request->input('quotation_id'));

        return $this->success([
            'success' => true,
            'message' => "Purchase Order #{$po->po_number} generated successfully.",
            'purchase_order' => $po
        ], 201);
    }

    /**
     * Show PO details.
     */
    public function show(int $id): JsonResponse
    {
        $po = $this->poService->get($id);
        return $this->success($po);
    }
}
