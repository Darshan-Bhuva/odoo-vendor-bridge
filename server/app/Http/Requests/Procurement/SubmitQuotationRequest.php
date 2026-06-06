<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class SubmitQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_days' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.rfq_item_id' => 'required|integer|exists:rfq_items,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
