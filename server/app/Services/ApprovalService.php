<?php

namespace App\Services;

use App\Models\Rfq;
use App\Models\Quotation;
use App\Models\Approval;
use App\Enums\Procurement\ApprovalStatusEnum;
use App\Enums\Procurement\QuotationStatusEnum;
use App\Enums\Procurement\RfqStatusEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class ApprovalService
{
    /**
     * Submit a selected quotation for Manager approval (Procurement Officer).
     */
    public function submit(int $rfqId, int $quotationId, ?string $remarks = null): Approval
    {
        $rfq = Rfq::findOrFail($rfqId);
        $quotation = Quotation::findOrFail($quotationId);

        if ($quotation->rfq_id !== $rfqId) {
            throw new CustomException("This quotation does not belong to the specified RFQ.");
        }

        // Only allow submitting quotations that are submitted
        if ($quotation->status->value !== QuotationStatusEnum::SUBMITTED->value) {
            throw new CustomException("This quotation has already been processed or is not submitted.");
        }

        return DB::transaction(function () use ($rfq, $quotation, $remarks) {
            // Cancel any prior pending approval request for this RFQ
            Approval::where('rfq_id', $rfq->id)
                ->where('status', ApprovalStatusEnum::PENDING->value)
                ->delete();

            // Set Quotation status to Selected
            $quotation->update(['status' => QuotationStatusEnum::SELECTED->value]);

            // Set RFQ status to Closed while pending approval
            $rfq->update(['status' => RfqStatusEnum::CLOSED->value]);

            // Find any manager user to assign approval (or let role manager act on it)
            // For simple structure, we just log the approval request record.
            // When a manager acts, we record their user_id as approver_id.
            $approval = Approval::create([
                'rfq_id' => $rfq->id,
                'quotation_id' => $quotation->id,
                'approver_id' => auth('api')->id(), // temporarily set, overwritten by actual approver on action
                'status' => ApprovalStatusEnum::PENDING->value,
                'remarks' => $remarks,
            ]);

            // Log activity
            ActivityLogService::log(
                'Approval',
                Approval::class,
                $approval->id,
                'submitted',
                "Quotation #{$quotation->quotation_number} submitted for Manager approval."
            );

            return $approval;
        });
    }

    /**
     * Approve or Reject a quotation (Manager / Approver).
     */
    public function action(int $approvalId, string $action, ?string $remarks = null): Approval
    {
        $approval = Approval::findOrFail($approvalId);
        $rfq = $approval->rfq;
        $quotation = $approval->quotation;

        if ($approval->status->value !== ApprovalStatusEnum::PENDING->value) {
            throw new CustomException("This approval request has already been processed.");
        }

        return DB::transaction(function () use ($approval, $rfq, $quotation, $action, $remarks) {
            $managerId = auth('api')->id();

            if ($action === 'approved') {
                $approval->update([
                    'status' => ApprovalStatusEnum::APPROVED->value,
                    'approver_id' => $managerId,
                    'remarks' => $remarks ?? $approval->remarks,
                    'approved_at' => now(),
                ]);

                // Update RFQ status to Approved and assign selected_quotation_id
                $rfq->update([
                    'status' => RfqStatusEnum::APPROVED->value,
                    'selected_quotation_id' => $quotation->id,
                ]);

                // Mark other quotations for this RFQ as rejected
                Quotation::where('rfq_id', $rfq->id)
                    ->where('id', '!=', $quotation->id)
                    ->update(['status' => QuotationStatusEnum::REJECTED->value]);

                ActivityLogService::log(
                    'Approval',
                    Approval::class,
                    $approval->id,
                    'approved',
                    "Quotation #{$quotation->quotation_number} approved by Manager."
                );
            } elseif ($action === 'rejected') {
                $approval->update([
                    'status' => ApprovalStatusEnum::REJECTED->value,
                    'approver_id' => $managerId,
                    'remarks' => $remarks ?? $approval->remarks,
                    'rejected_at' => now(),
                ]);

                // Revert Quotation status to rejected
                $quotation->update(['status' => QuotationStatusEnum::REJECTED->value]);

                // Revert RFQ to closed so another quote can be compared and selected
                $rfq->update([
                    'status' => RfqStatusEnum::CLOSED->value,
                ]);

                ActivityLogService::log(
                    'Approval',
                    Approval::class,
                    $approval->id,
                    'rejected',
                    "Quotation #{$quotation->quotation_number} rejected by Manager."
                );
            } else {
                throw new CustomException("Invalid approval action specified.");
            }

            return $approval;
        });
    }

    /**
     * Get pending approvals.
     */
    public function getPending()
    {
        return Approval::with(['rfq', 'quotation.vendor', 'approver'])
            ->where('status', ApprovalStatusEnum::PENDING->value)
            ->latest()
            ->paginate(config('site.pagination_limit', 10));
    }
}
