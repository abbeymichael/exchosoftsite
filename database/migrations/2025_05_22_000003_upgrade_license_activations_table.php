<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Upgrade license_activations table with:
 * - fingerprint (hashed hardware signature)
 * - os, app_version, country, activation_source
 * - is_suspicious flag + suspicious_reason
 * - hardware_id (optional; null = web/cloud activation)
 * - expires_at (for auto-expiry of inactive activations)
 * - performance indexes
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_activations', function (Blueprint $table) {
            // Device fingerprint (hashed, stored for tamper detection)
            $table->string('fingerprint', 64)->nullable()->after('ip_address');

            // OS & version details
            $table->string('os', 64)->nullable()->after('fingerprint');
            $table->string('app_version', 32)->nullable()->after('os');

            // Geo & source
            $table->string('country', 2)->nullable()->after('app_version')
                ->comment('ISO 3166-1 alpha-2');
            $table->string('activation_source', 32)->nullable()->after('country')
                ->comment('api|web|desktop|mobile|trial');

            // Optional hardware lock (null = no hardware binding e.g. web app)
            $table->string('hardware_id')->nullable()->after('device_id')
                ->comment('Optional hardware fingerprint for locked licenses');

            // Suspicious activity
            $table->boolean('is_suspicious')->default(false)->after('status');
            $table->string('suspicious_reason')->nullable()->after('is_suspicious');

            // Auto-expiry for inactive activations
            $table->timestamp('expires_at')->nullable()->after('deactivated_at');

            // Indexes
            $table->index('fingerprint');
            $table->index('status');
            $table->index('last_seen_at');
            $table->index('is_suspicious');
            $table->index(['license_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('license_activations', function (Blueprint $table) {
            $table->dropIndex(['fingerprint']);
            $table->dropIndex(['status']);
            $table->dropIndex(['last_seen_at']);
            $table->dropIndex(['is_suspicious']);
            $table->dropIndex(['license_id', 'status']);
            $table->dropColumn([
                'fingerprint', 'os', 'app_version', 'country', 'activation_source',
                'hardware_id', 'is_suspicious', 'suspicious_reason', 'expires_at',
            ]);
        });
    }
};
