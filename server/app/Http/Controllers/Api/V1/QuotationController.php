<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\SubmitQuotationRequest;
use App\Traits\ApiResponser;
use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Http\JsonResponse;

#[Group('Quatation', weight: 60)]
class QuotationController extends Controller
{
    use ApiResponser;

    private QuotationService $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
    * List quotations for the authenticated vendor (or all for admin).
    */
    public function index(): JsonResponse
    {
        $user = auth('api')->user();
        $query = Quotation::with(['vendor', 'items']);
        if ($user->hasRole(config('site.roles.vendor'))) {
            $vendor = $user->vendor;
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            } else {
                return $this->success([], 200);
            }
        }
        $quotations = $query->orderByDesc('created_at')->paginate(config('site.pagination_limit', 10));
        return $this->success($quotations);
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
