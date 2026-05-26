<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateTrialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_id'     => 'required|integer|exists:products,id',
            'customer_id'    => 'nullable|integer|exists:customers,id',
            'customer_email' => 'nullable|email|max:255',
            'trial_days'     => 'nullable|integer|min:1|max:365',
            'edition'        => 'nullable|in:standard,professional,enterprise',
            'max_activations' => 'nullable|integer|min:1|max:5',
            'metadata'        => 'nullable|array',
            // Enterprise licensing (Phase 2)
            'features'        => 'nullable|array',
            'features.*'      => 'string|max:100',
            'min_app_version' => 'nullable|string|max:32',
            'max_app_version' => 'nullable|string|max:32',
        ];
    }
}
