<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Models\Rfq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;
use App\Http\Resources\Procurement\RfqResource;

/**
 * @tags Vendor RFQs
 */
#[Middleware(['auth:api'])]
class VendorRfqController extends Controller
{
    use ApiResponser;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return $this->error('Vendor profile not found.', 404);
        }

        $rfqs = $vendor->rfqs()->latest()->paginate(config('site.pagination.per_page', 10));

        return $this->success(
            RfqResource::collection($rfqs)
        );
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $user = $request->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            return $this->error('Vendor profile not found.', 404);
        }

        $rfq = $vendor->rfqs()->where('rfqs.id', $id)->firstOrFail();

        return $this->success(
            new RfqResource($rfq)
        );
    }
}
