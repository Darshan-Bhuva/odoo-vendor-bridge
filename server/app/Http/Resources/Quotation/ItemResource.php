<?php

namespace App\Http\Resources\Quotation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rfq_item_id' => $this->rfq_item_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'delivery_days' => $this->delivery_days,
            'line_total' => $this->line_total,
        ];
    }
}
