<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores every inbound validate/status request for analytics,
 * failed-attempt detection, and replay-attack prevention.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_logs', function (Blueprint $table) {
            $table->id();

            $table->string('license_key', 60)->nullable()
                ->comment('The key that was submitted (may not exist)');
            $table->foreignId('license_id')->nullable()->constrained('licenses')->nullOnDelete();
            $table->string('product_slug')->nullable();

            $table->enum('action', ['validate', 'status', 'renew', 'deactivate'])->default('validate');
            $table->boolean('success')->default(false);
            $table->string('failure_reason')->nullable();

            // Request context
            $table->string('device_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('app_version', 32)->nullable();
            $table->string('platform', 32)->nullable();
            $table->string('country', 2)->nullable();

            // Replay-attack prevention
            $table->string('request_nonce', 64)->nullable()->index()
                ->comment('SHA-256 of timestamp+license+device; reject duplicates within TTL');
            $table->timestamp('request_timestamp')->nullable()
                ->comment('Timestamp claimed by the client; reject if skew > 5 min');

            $table->timestamp('created_at')->useCurrent();

            $table->index('license_key');
            $table->index('license_id');
            $table->index('success');
            $table->index('ip_address');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_logs');
    }
};
