<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30; // seconds between retries

    public function __construct(
        public readonly string $url,
        public readonly array  $payload,
        public readonly string $signature,
        public readonly int    $productId,
    ) {}

    public function handle(): void
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type'          => 'application/json',
                    'X-ExchoLicense-Event'  => $this->payload['event'] ?? 'unknown',
                    'X-ExchoLicense-Sig'    => $this->signature,
                    'X-ExchoLicense-TS'     => $this->payload['timestamp'] ?? now()->toISOString(),
                ])
                ->post($this->url, $this->payload);

            if (! $response->successful()) {
                Log::warning('Webhook delivery failed', [
                    'url'        => $this->url,
                    'status'     => $response->status(),
                    'product_id' => $this->productId,
                    'event'      => $this->payload['event'] ?? null,
                ]);

                $this->fail("Webhook returned HTTP {$response->status()}");
            }
        } catch (\Throwable $e) {
            Log::error('Webhook exception', [
                'url'        => $this->url,
                'error'      => $e->getMessage(),
                'product_id' => $this->productId,
            ]);

            $this->fail($e);
        }
    }
}
