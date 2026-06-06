<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class CreateVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|max:255',
            'category' => 'nullable|max:100',
            'gst_number' => 'nullable|max:50',
            'contact_person' => 'required|max:255',
            'email' => 'required|email|max:190|unique:vendors,email',
            'address' => 'nullable|max:500',
            'status' => 'required|in:active,inactive,pending,blocked',
        ];
    }
}
