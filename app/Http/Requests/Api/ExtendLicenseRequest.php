<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ExtendLicenseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'license_key'   => 'required|string|exists:licenses,license_key',
            'days'          => 'required_without:expires_at|integer|min:1|max:36500',
            'expires_at'    => 'required_without:days|date|after:today',
        ];
    }
}
