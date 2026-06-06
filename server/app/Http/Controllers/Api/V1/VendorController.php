<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Services\VendorService;
use App\Http\Requests\Vendor\CreateVendorRequest;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use App\Http\Resources\Vendor\Resource as VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;

/**
 * @tags Vendor Management
 */
#[Middleware(['auth:api'])]
class VendorController extends Controller
{
    use ApiResponser;

    private VendorService $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Vendor::class);

        $vendors = $this->vendorService->all($request->only(['search', 'status', 'category']));
        
        return $this->success(
            VendorResource::collection($vendors)
        );
    }

    public function store(CreateVendorRequest $request): JsonResponse
    {
        $this->authorize('create', Vendor::class);

        $vendor = $this->vendorService->store($request->validated());

        return $this->success(
            new VendorResource($vendor),
            'Vendor created successfully.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $vendor = Vendor::findOrFail($id);
        $this->authorize('view', $vendor);

        return $this->success(
            new VendorResource($vendor)
        );
    }

    public function update(UpdateVendorRequest $request, int $id): JsonResponse
    {
        $vendorModel = Vendor::findOrFail($id);
        $this->authorize('update', $vendorModel);

        $vendor = $this->vendorService->update($id, $request->validated());

        return $this->success(
            new VendorResource($vendor),
            'Vendor updated successfully.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $vendor = Vendor::findOrFail($id);
        $this->authorize('delete', $vendor);

        $this->vendorService->destroy($id);

        return $this->success(
            null,
            'Vendor deleted successfully.'
        );
    }
}
