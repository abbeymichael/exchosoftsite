<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BulkCreateLicenseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_id'      => 'required|integer|exists:products,id',
            'label'           => 'required|string|max:255',
            'quantity'        => 'required|integer|min:1|max:10000',
            'key_prefix'      => 'nullable|string|max:8|alpha_num',
            'edition'         => 'nullable|in:standard,professional,enterprise,trial',
            'license_type'    => 'nullable|in:lifetime,monthly,annual,yearly,trial,custom',
            'max_activations' => 'nullable|integer|min:1|max:9999',
            'expires_at'      => 'nullable|date|after:today',
            'duration_days'   => 'nullable|integer|min:1|max:36500',
            'reseller_tag'    => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:1000',
            'metadata'        => 'nullable|array',
        ];
    }
}
