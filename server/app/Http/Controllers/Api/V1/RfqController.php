<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Http\Requests\Procurement\CreateRfqRequest;
use App\Services\RfqService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;

#[Group('Rfq', weight: 60)]
class RfqController extends Controller
{
    use ApiResponser;

    private RfqService $rfqService;

    public function __construct(RfqService $rfqService)
    {
        $this->rfqService = $rfqService;
    }

    /**
     * List all RFQs (filtered based on role & search parameters).
     */
    public function index(Request $request): JsonResponse
    {
        $rfqs = $this->rfqService->list($request->only(['status', 'search']));
        return $this->success($rfqs);
    }

    /**
     * Create a new RFQ (Procurement Officer / Admin only).
     */
    public function store(CreateRfqRequest $request): JsonResponse
    {
        $rfq = $this->rfqService->create($request->validated());
        return $this->success([
            'success' => true,
            'message' => 'RFQ created successfully and invitation emails queued.',
            'rfq' => $rfq
        ], 201);
    }

    /**
     * Show detailed RFQ.
     */
    public function show(int $id): JsonResponse
    {
        $rfq = $this->rfqService->get($id);
        return $this->success($rfq);
    }

    /**
     * Close an RFQ (Procurement Officer / Admin only).
     */
    public function close(int $id): JsonResponse
    {
        $rfq = $this->rfqService->close($id);
        return $this->success([
            'success' => true,
            'message' => 'RFQ closed successfully.',
            'rfq' => $rfq
        ]);
    }
}
