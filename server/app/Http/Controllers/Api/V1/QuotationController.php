<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Http\Requests\Quotation\CreateQuotationRequest;
use App\Http\Requests\Quotation\UpdateQuotationRequest;
use App\Services\QuotationService;
use App\Models\Quotation;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;
use App\Http\Resources\Quotation\Resource as QuotationResource;

/**
 * @tags Vendor Quotations
 */
#[Middleware(['auth:api'])]
class QuotationController extends Controller
{
    use ApiResponser;

    private QuotationService $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    public function store(CreateQuotationRequest $request): JsonResponse
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
