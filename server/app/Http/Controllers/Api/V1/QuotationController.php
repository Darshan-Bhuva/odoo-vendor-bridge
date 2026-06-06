<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Http\Requests\Procurement\SubmitQuotationRequest;
use App\Services\QuotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;

#[Middleware(['auth:api'])]
class QuotationController extends Controller
{
    use ApiResponser;

    private QuotationService $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
     * Submit a quotation for an RFQ (Vendor only).
     */
    public function store(int $rfqId, SubmitQuotationRequest $request): JsonResponse
    {
        $quotation = $this->quotationService->submit($rfqId, $request->validated());
        return $this->success([
            'success' => true,
            'message' => 'Quotation submitted successfully.',
            'quotation' => $quotation
        ], 201);
    }

    /**
     * Compare all quotations submitted for an RFQ (Procurement Officer / Manager / Admin).
     */
    public function compare(int $rfqId): JsonResponse
    {
        $comparison = $this->quotationService->compare($rfqId);
        return $this->success($comparison);
    }
}
