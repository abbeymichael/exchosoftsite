<?php

namespace App\Services;

use App\Jobs\SendWebhookJob;
use App\Models\Product;

class WebhookService
{
    /**
     * Queue a webhook payload to be delivered to the product's webhook_url.
     *
     * The payload is signed with the product's secret_key using HMAC-SHA256
     * so the receiving end can verify authenticity.
     *
     * @param  Product  $product
     * @param  string   $event    e.g. 'license.created', 'license.revoked'
     * @param  array    $data     Event data
     */
    public static function dispatch(Product $product, string $event, array $data): void
    {
        if (empty($product->webhook_url)) {
            return;
        }

        $payload = [
            'event'      => $event,
            'product'    => $product->slug,
            'timestamp'  => now()->toISOString(),
            'data'       => $data,
        ];

        $signature = self::sign($payload, $product->secret_key);

        SendWebhookJob::dispatch(
            url: $product->webhook_url,
            payload: $payload,
            signature: $signature,
            productId: $product->id,
        )->onQueue('webhooks');
    }

    /**
     * HMAC-SHA256 signature for webhook payload verification.
     */
    public static function sign(array $payload, string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload), $secret);
    }

    /**
     * Verify an incoming webhook signature (for inbound webhooks from payment providers).
     */
    public static function verify(string $rawBody, string $receivedSignature, string $secret): bool
    {
        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, $receivedSignature);
    }
}
