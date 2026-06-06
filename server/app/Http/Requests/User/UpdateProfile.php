<?php

namespace App\Http\Requests\User;

use App\Rules\MediaRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfile extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|max:120',
            'last_name' => 'required|max:120',
            'username' => [
                'required',
                'max:120',
                \Illuminate\Validation\Rule::unique('users', 'username')->ignore(Auth::guard('api')->id()),
            ],
            'mobile_no' => 'nullable|min:8|max:15',
        ] + MediaRule::rules(config('media.tags.profile'), true);
    }
}
