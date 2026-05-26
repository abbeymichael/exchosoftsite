<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs every API request for analytics and dashboard counters.
 *
 * Captures: endpoint name, HTTP status, success flag, IP, licence key,
 * device_id, platform, app_version, and request duration.
 */
class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        try {
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            // Determine endpoint label from route name or URI segment
            $routeName = $request->route()?->getName() ?? '';
            $endpoint  = $this->resolveEndpoint($routeName, $request);

            // Parse JSON body (already consumed by now — re-read decoded)
            $body = $request->all();

            // Determine success: 2xx AND (no "valid: false" in body)
            $statusCode = $response->getStatusCode();
            $success    = $statusCode >= 200 && $statusCode < 300;

            // Peek at JSON response body for error_code
            $errorCode = null;
            if (method_exists($response, 'getData')) {
                $data = $response->getData(true);
                if (isset($data['valid']) && $data['valid'] === false) {
                    $success   = false;
                    $errorCode = $data['error_code'] ?? null;
                }
            }

            ApiRequestLog::create([
                'endpoint'     => $endpoint,
                'method'       => $request->method(),
                'route_name'   => $routeName,
                'http_status'  => $statusCode,
                'success'      => $success,
                'error_code'   => $errorCode,
                'ip_address'   => $request->ip(),
                'license_key'  => strtoupper($body['license_key'] ?? ''),
                'product_slug' => $body['product'] ?? null,
                'device_id'    => $body['device_id'] ?? null,
                'platform'     => $body['platform'] ?? null,
                'app_version'  => $body['app_version'] ?? null,
                'duration_ms'  => $durationMs,
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the API
            Log::error('ApiRequestLog write failed: ' . $e->getMessage());
        }

        return $response;
    }

    private function resolveEndpoint(string $routeName, Request $request): string
    {
        // Map named routes → short labels
        $map = [
            'api.v1.licenses.validate'          => 'validate',
            'api.v1.licenses.status'            => 'status',
            'api.v1.licenses.renew'             => 'renew',
            'api.v1.licenses.deactivate'        => 'deactivate',
            'api.v1.internal.licenses.create'   => 'internal.create',
            'api.v1.internal.licenses.bulk_create' => 'internal.bulk_create',
            'api.v1.internal.licenses.create_trial' => 'internal.create_trial',
            'api.v1.internal.licenses.extend'   => 'internal.extend',
            'api.v1.internal.licenses.revoke'   => 'internal.revoke',
            'api.v1.internal.licenses.suspend'  => 'internal.suspend',
            'api.v1.internal.licenses.unsuspend' => 'internal.unsuspend',
            'api.v1.internal.licenses.reset_devices' => 'internal.reset_devices',
            'api.v1.internal.licenses.regenerate_key' => 'internal.regenerate_key',
            'api.v1.internal.licenses.attach_notes' => 'internal.attach_notes',
            'api.v1.internal.licenses.show'     => 'internal.show',
        ];

        return $map[$routeName] ?? ltrim($request->getPathInfo(), '/');
    }
}
