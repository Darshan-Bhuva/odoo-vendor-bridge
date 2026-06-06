<?php

namespace App\Http\Resources\Quotation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quotation_number' => $this->quotation_number,
            'rfq_id' => $this->rfq_id,
            'vendor_id' => $this->vendor_id,
            'tax_percent' => $this->tax_percent,
            'notes' => $this->notes,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'items' => ItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
