<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_activations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('license_id')->constrained()->cascadeOnDelete();

            // Device identity
            $table->string('device_id');
            $table->string('device_name')->nullable();
            $table->string('hardware_id')->nullable()->comment('Optional hardware fingerprint for locked licenses');
            $table->string('platform')->nullable();
            $table->string('app_type', 20)->default('desktop')->comment('desktop|web|cloud|hybrid');
            $table->string('ip_address')->nullable();

            // Device fingerprint (hashed, stored for tamper detection)
            $table->string('fingerprint', 64)->nullable();

            // OS & version details
            $table->string('os', 64)->nullable();
            $table->string('app_version', 32)->nullable();

            // Geo & source
            $table->string('country', 2)->nullable()->comment('ISO 3166-1 alpha-2');
            $table->string('activation_source', 32)->nullable()->comment('api|web|desktop|mobile|trial');

            // Status
            $table->enum('status', ['active', 'deactivated', 'revoked'])->default('active');

            // Suspicious activity
            $table->boolean('is_suspicious')->default(false);
            $table->string('suspicious_reason')->nullable();

            // Timestamps
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamp('expires_at')->nullable()->comment('Auto-expiry for inactive activations');

            $table->text('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('license_id');
            $table->index('fingerprint');
            $table->index('status');
            $table->index('last_seen_at');
            $table->index('is_suspicious');
            $table->index(['license_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_activations');
    }
};
