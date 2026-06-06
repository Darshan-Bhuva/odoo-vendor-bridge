<?php

namespace App\Services;

use App\Models\Rfq;
use App\Services\ActivityLogService;
use App\Models\RfqItem;
use App\Enums\Procurement\RfqStatusEnum;
use App\Enums\Procurement\RfqVendorInvitationStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RfqService
{
    /**
     * Create a new RFQ with items and invite vendors.
     */
    public function create(array $inputs): Rfq
    {
        return DB::transaction(function () use ($inputs) {
            $year = now()->year;
            $latestRfq = Rfq::latest('id')->first();
            $nextId = $latestRfq ? $latestRfq->id + 1 : 1;
            $rfqNumber = 'RFQ-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $rfq = Rfq::create([
                'created_by' => auth('api')->id(),
                'rfq_number' => $rfqNumber,
                'title' => $inputs['title'],
                'description' => $inputs['description'] ?? null,
                'deadline' => $inputs['deadline'],
                'status' => $inputs['status'] ?? RfqStatusEnum::OPEN->value,
            ]);

            // Save RFQ items
            if (isset($inputs['items']) && is_array($inputs['items'])) {
                foreach ($inputs['items'] as $item) {
                    RfqItem::create([
                        'rfq_id' => $rfq->id,
                        'item_name' => $item['item_name'],
                        'description' => $item['description'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                    ]);
                }
            }

            // Invite Vendors
            if (isset($inputs['vendor_ids']) && is_array($inputs['vendor_ids'])) {
                $syncData = [];
                foreach ($inputs['vendor_ids'] as $vendorId) {
                    $syncData[$vendorId] = [
                        'invitation_status' => RfqVendorInvitationStatusEnum::INVITED->value,
                        'invited_at' => now(),
                    ];
                }
                $rfq->vendors()->sync($syncData);
            }

            // Log activity
            ActivityLogService::log(
                'RFQ',
                Rfq::class,
                $rfq->id,
                'created',
                "RFQ #{$rfq->rfq_number} created with " . count($inputs['items'] ?? []) . " items."
            );

            return $rfq->load(['items', 'vendors']);
        });
    }

    /**
     * List RFQs filtered by role and status.
     */
    public function list(array $filters = [])
    {
        $user = auth('api')->user();
        $query = Rfq::with(['creator', 'items']);

        // Vendors can only see RFQs they are invited to
        if ($user->hasRole(config('site.roles.vendor'))) {
            $vendor = $user->vendor;
            if (!$vendor) {
                return collect();
            }

            $query->whereHas('vendors', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            });

            // Vendors should not see draft RFQs
            $query->where('status', '!=', RfqStatusEnum::DRAFT->value);
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('rfq_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(config('site.pagination_limit', 10));
    }

    /**
     * Get details of a single RFQ.
     */
    public function get(int $id): Rfq
    {
        $user = auth('api')->user();
        $rfq = Rfq::with(['items', 'vendors', 'quotations.vendor', 'selectedQuotation.vendor'])->findOrFail($id);

        // Track viewing by Vendor
        if ($user->hasRole(config('site.roles.vendor'))) {
            $vendor = $user->vendor;
            if ($vendor) {
                $invitation = DB::table('rfq_vendor')
                    ->where('rfq_id', $rfq->id)
                    ->where('vendor_id', $vendor->id)
                    ->first();

                if ($invitation && $invitation->invitation_status === RfqVendorInvitationStatusEnum::INVITED->value) {
                    DB::table('rfq_vendor')
                        ->where('rfq_id', $rfq->id)
                        ->where('vendor_id', $vendor->id)
                        ->update([
                            'invitation_status' => RfqVendorInvitationStatusEnum::VIEWED->value,
                        ]);
                }
            }
        }

        return $rfq;
    }

    /**
     * Close an RFQ (preventing further quotation submissions).
     */
    public function close(int $id): Rfq
    {
        $rfq = Rfq::findOrFail($id);
        $rfq->update(['status' => RfqStatusEnum::CLOSED->value]);

        ActivityLogService::log(
            'RFQ',
            Rfq::class,
            $rfq->id,
            'closed',
            "RFQ #{$rfq->rfq_number} has been manually closed."
        );

        return $rfq;
    }
}
