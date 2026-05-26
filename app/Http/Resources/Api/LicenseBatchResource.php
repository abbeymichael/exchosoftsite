<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->uuid,
            'batch_code'      => $this->batch_code,
            'label'           => $this->label,
            'product'         => $this->when($this->relationLoaded('product'), fn () => [
                'id'   => $this->product->uuid,
                'name' => $this->product->name,
            ]),
            'key_prefix'      => $this->key_prefix,
            'quantity'        => $this->quantity,
            'total_generated' => $this->total_generated,
            'total_used'      => $this->total_used,
            'total_revoked'   => $this->total_revoked,
            'usage_percent'   => $this->usage_percent,
            'license_type'    => $this->license_type,
            'edition'         => $this->edition,
            'max_activations' => $this->max_activations,
            'expires_at'      => $this->expires_at?->toDateString(),
            'status'          => $this->status,
            'reseller_tag'    => $this->reseller_tag,
            'created_at'      => $this->created_at->toISOString(),
        ];
    }
}
