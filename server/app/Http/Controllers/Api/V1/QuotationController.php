<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\SubmitQuotationRequest;
use App\Traits\ApiResponser;
use App\Models\Quotation;
use App\Services\QuotationService;
use App\Models\Quotation;
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
        $this->authorize('create', Quotation::class);

        $quotation = $this->quotationService->store($request->validated());

        return $this->success(
            new QuotationResource($quotation),
            'Quotation saved successfully.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $quotation = Quotation::findOrFail($id);
        $this->authorize('view', $quotation);

        return $this->success(
            new QuotationResource($quotation->load('items'))
        );
    }

    public function update(int $id, UpdateQuotationRequest $request): JsonResponse
    {
        $quotation = Quotation::findOrFail($id);
        $this->authorize('update', $quotation);

        $updatedQuotation = $this->quotationService->update($id, $request->validated());

        return $this->success(
            new QuotationResource($updatedQuotation),
            'Quotation updated successfully.'
        );
    }

    public function submit(int $id): JsonResponse
    {
        $quotation = Quotation::findOrFail($id);
        $this->authorize('update', $quotation);

        $submittedQuotation = $this->quotationService->submit($id);

        return $this->success(
            new QuotationResource($submittedQuotation),
            'Quotation submitted successfully.'
        );
    }
}
