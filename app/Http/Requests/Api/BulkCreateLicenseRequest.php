<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BulkCreateLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required_without:product_code|exists:products,id',
            'product_code' => 'required_without:product_id|string|exists:products,product_code',
            'label' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:10000',
            'key_prefix' => 'nullable|string|max:8|alpha_num',
            'edition' => 'nullable|in:standard,professional,enterprise,trial',
            'license_type' => 'required|in:lifetime,monthly,annual,yearly,trial,custom',
            'duration_days' => 'required_without:expires_at|nullable|integer|min:1|max:36500',
            'expires_at' => 'required_without:duration_days|nullable|date|after:today',
            'max_activations' => 'nullable|integer|min:1|max:9999',
            'reseller_tag' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ];
    }
}
