<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RevokeLicenseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'license_key' => 'required|string|exists:licenses,license_key',
            'reason'      => 'nullable|string|max:500',
        ];
    }
}
