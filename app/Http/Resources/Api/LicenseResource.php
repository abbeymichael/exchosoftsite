<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Stable internal UUID (survives key regeneration)
            'id'                  => $this->uuid,
            // Human-facing key
            'license_key'         => $this->license_key,
            'product'             => $this->when($this->relationLoaded('product'), fn () => [
                'id'              => $this->product->uuid,
                'name'            => $this->product->name,
                'slug'            => $this->product->slug,
                'app_identifier'  => $this->product->app_identifier,
                'min_app_version' => $this->product->min_app_version,
                'max_app_version' => $this->product->max_app_version,
            ]),
            'customer'            => $this->when($this->relationLoaded('customer') && $this->customer, fn () => [
                'id'      => $this->customer->uuid,
                'name'    => $this->customer->name,
                'email'   => $this->customer->email,
                'company' => $this->customer->company,
            ]),
            'edition'             => $this->edition,
            'type'                => $this->type,
            'status'              => $this->status,
            'max_activations'     => $this->max_activations,
            'current_activations' => $this->current_activations,
            'expires_at'          => $this->expires_at?->toDateString(),
            'expires_in_days'     => $this->expires_at ? max(0, (int) now()->diffInDays($this->expires_at, false)) : null,
            'is_lifetime'         => $this->expires_at === null,
            'is_expired'          => $this->isExpired(),
            'is_valid'            => $this->isValid(),
            'in_grace_period'     => $this->isInGracePeriod(),
            'grace_period_days'   => $this->grace_period_days,
            'is_renewable'        => $this->is_renewable,
            'order_id'            => $this->order_id,
            'transaction_id'      => $this->transaction_id,
            'reseller_id'         => $this->reseller_id,
            'support_tier'        => $this->support_tier,
            'batch_id'            => $this->batch_id,
            // Enterprise licensing fields
            'features'            => $this->features ?? [],
            'revocation_checksum' => $this->revocation_checksum,
            'min_app_version'     => $this->min_app_version,
            'max_app_version'     => $this->max_app_version,
            'created_at'          => $this->created_at->toISOString(),
            'updated_at'          => $this->updated_at->toISOString(),
        ];
    }
}
