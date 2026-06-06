<?php

namespace App\Http\Resources\Vendor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_code' => $this->vendor_code,
            'company_name' => $this->company_name,
            'category' => $this->category,
            'gst_number' => $this->gst_number,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'address' => $this->address,
            'status' => $this->status,
        ];
    }
}
