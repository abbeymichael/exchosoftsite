<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks every inbound API request per endpoint for analytics / dashboard.
 * Lightweight — no relations, just counters and raw context.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();

            // Endpoint identity
            $table->string('endpoint', 100)
                ->comment('e.g. validate|status|renew|deactivate|internal.create …');
            $table->string('method', 10)->default('POST');
            $table->string('route_name')->nullable();

            // Request outcome
            $table->smallInteger('http_status')->default(200);
            $table->boolean('success')->default(true);
            $table->string('error_code', 60)->nullable();

            // Client context
            $table->string('ip_address', 45)->nullable();
            $table->string('license_key', 60)->nullable()->index();
            $table->string('product_slug')->nullable();
            $table->string('device_id')->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('app_version', 32)->nullable();

            // Timing
            $table->unsignedInteger('duration_ms')->nullable()
                ->comment('Request-to-response time in milliseconds');

            $table->timestamp('created_at')->useCurrent();

            // Indexes for dashboard aggregations
            $table->index('endpoint');
            $table->index('success');
            $table->index('http_status');
            $table->index('created_at');
            $table->index(['endpoint', 'created_at']);
            $table->index(['endpoint', 'success']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};
