<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Internal Provisioning API — Create License
 *
 * Only product_id is required. Customer fields are all optional:
 *   - Pass customer_email to auto-create/match a customer
 *   - Pass customer_id if you already have the ID
 *   - Omit both for an unassigned (pre-sale / bulk) key
 */
class CreateLicenseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // ── Required ──────────────────────────────────────────────────────
            'product_id'        => 'required|integer|exists:products,id',

            // ── Customer — all optional ───────────────────────────────────────
            'customer_id'       => 'nullable|integer|exists:customers,id',
            'customer_email'    => 'nullable|email|max:255',
            'customer_name'     => 'nullable|string|max:255',
            'company'           => 'nullable|string|max:255',

            // ── License properties — sensible defaults applied in service ─────
            'edition'           => 'nullable|in:standard,professional,enterprise,trial',
            'type'              => 'nullable|in:lifetime,monthly,annual,yearly,trial,floating,multi-device,custom',
            'max_activations'   => 'nullable|integer|min:1|max:9999',

            // ── Expiry — pick one method or omit for lifetime ─────────────────
            'expires_at'        => 'nullable|date|after:today',
            'duration_days'     => 'nullable|integer|min:1|max:36500',

            // ── Commerce / tracking ───────────────────────────────────────────
            'order_id'          => 'nullable|string|max:100',
            'transaction_id'    => 'nullable|string|max:100',
            'reseller_id'       => 'nullable|string|max:100',

            // ── Misc ──────────────────────────────────────────────────────────
            'support_tier'      => 'nullable|in:standard,priority,enterprise',
            'notes'             => 'nullable|string|max:1000',
            'metadata'          => 'nullable|array',
            'grace_period_days' => 'nullable|integer|min:0|max:90',
            'key_prefix'        => 'nullable|string|max:8|alpha_num',
            'is_renewable'      => 'nullable|boolean',

            // ── Enterprise licensing (Phase 2) ────────────────────────────────
            'features'          => 'nullable|array',
            'features.*'        => 'string|max:100',
            'min_app_version'   => 'nullable|string|max:32',
            'max_app_version'   => 'nullable|string|max:32',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'A product_id is required to create a license.',
            'product_id.exists'   => 'The specified product does not exist.',
            'customer_email.email' => 'customer_email must be a valid email address.',
        ];
    }
}
