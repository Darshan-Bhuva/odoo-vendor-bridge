<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Http\Requests\Procurement\ApprovalActionRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;

#[Middleware(['auth:api'])]
class ApprovalController extends Controller
{
    use ApiResponser;

    private ApprovalService $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Submit a quotation for Manager approval (Procurement Officer only).
     */
    public function submit(Request $request, int $rfqId): JsonResponse
    {
        $request->validate([
            'quotation_id' => 'required|integer|exists:quotations,id',
            'remarks' => 'nullable|string|max:500',
        ]);

        $approval = $this->approvalService->submit(
            $rfqId,
            $request->input('quotation_id'),
            $request->input('remarks')
        );

        return $this->success([
            'success' => true,
            'message' => 'Quotation submitted for approval successfully.',
            'approval' => $approval
        ], 201);
    }

    /**
     * Approve or reject a quotation approval request (Manager / Approver only).
     */
    public function action(ApprovalActionRequest $request, int $id): JsonResponse
    {
        $approval = $this->approvalService->action(
            $id,
            $request->input('action'),
            $request->input('remarks')
        );

        return $this->success([
            'success' => true,
            'message' => "Quotation approval state updated to '{$request->input('action')}'.",
            'approval' => $approval
        ]);
    }

    /**
     * Get pending approval requests (Manager / Approver only).
     */
    public function pending(): JsonResponse
    {
        $pending = $this->approvalService->getPending();
        return $this->success($pending);
    }
}
