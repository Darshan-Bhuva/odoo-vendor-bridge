<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vendorId = $this->route('vendor');

        return [
            'company_name' => 'sometimes|required|max:255',
            'category' => 'nullable|max:100',
            'gst_number' => 'nullable|max:50',
            'contact_person' => 'sometimes|required|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:190',
                Rule::unique('vendors', 'email')->ignore($vendorId),
            ],
            'address' => 'nullable|max:500',
            'status' => 'sometimes|required|in:active,inactive,pending,blocked',
        ];
    }
}
