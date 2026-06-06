<?php

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class VendorService
{
    public function __construct(private Vendor $vendor)
    {
    }

    public function all(array $filters = [])
    {
        $query = $this->vendor->query();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('vendor_code', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->latest()->paginate(config('site.pagination.per_page', 10));
    }

    public function store(array $data)
    {
        $data['vendor_code'] = strtoupper(Str::random(6));
        return $this->vendor->create($data);
    }

    public function update(int $id, array $data)
    {
        $vendor = $this->vendor->findOrFail($id);
        $vendor->update($data);
        return $vendor;
    }

    public function destroy(int $id)
    {
        $vendor = $this->vendor->findOrFail($id);
        return $vendor->delete();
    }
}
