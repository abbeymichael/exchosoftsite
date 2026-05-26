<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    public $timestamps = false; // only created_at

    protected $fillable = [
        'endpoint',
        'method',
        'route_name',
        'http_status',
        'success',
        'error_code',
        'ip_address',
        'license_key',
        'product_slug',
        'device_id',
        'platform',
        'app_version',
        'duration_ms',
    ];

    protected $casts = [
        'success'    => 'boolean',
        'created_at' => 'datetime',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // Factory helpers for the dashboard
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Total requests for a given endpoint (optionally in last N hours).
     */
    public static function countFor(string $endpoint, int $hours = 24): int
    {
        return self::where('endpoint', $endpoint)
            ->where('created_at', '>=', now()->subHours($hours))
            ->count();
    }

    /**
     * Success rate (0–100) for a given endpoint in the last N hours.
     */
    public static function successRate(string $endpoint, int $hours = 24): float
    {
        $total = self::countFor($endpoint, $hours);
        if ($total === 0) {
            return 100.0;
        }
        $success = self::where('endpoint', $endpoint)
            ->where('success', true)
            ->where('created_at', '>=', now()->subHours($hours))
            ->count();

        return round(($success / $total) * 100, 1);
    }

    /**
     * Aggregate stats for all tracked endpoints.
     *
     * @return array<string, array{total:int, success:int, failed:int, rate:float}>
     */
    public static function endpointSummary(int $hours = 24): array
    {
        $endpoints = ['validate', 'status', 'renew', 'deactivate', 'internal'];
        $summary   = [];

        foreach ($endpoints as $ep) {
            $total   = self::where('endpoint', 'like', $ep . '%')
                ->where('created_at', '>=', now()->subHours($hours))
                ->count();
            $success = self::where('endpoint', 'like', $ep . '%')
                ->where('success', true)
                ->where('created_at', '>=', now()->subHours($hours))
                ->count();

            $summary[$ep] = [
                'total'   => $total,
                'success' => $success,
                'failed'  => $total - $success,
                'rate'    => $total > 0 ? round(($success / $total) * 100, 1) : 100.0,
            ];
        }

        return $summary;
    }

    /**
     * Hourly request counts for the last 24 h (for sparkline charts).
     *
     * @return array<int, array{hour:string, count:int}>
     */
    public static function hourlyTrend(string $endpoint = null): array
    {
        $query = self::selectRaw(
            'DATE_FORMAT(created_at, \'%Y-%m-%d %H:00:00\') as hour, COUNT(*) as count'
        )
            ->where('created_at', '>=', now()->subHours(24))
            ->groupByRaw('DATE_FORMAT(created_at, \'%Y-%m-%d %H:00:00\')')
            ->orderBy('hour');

        if ($endpoint) {
            $query->where('endpoint', 'like', $endpoint . '%');
        }

        return $query->get()->toArray();
    }
}
