<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Upgrade licenses table with:
 * - customer linking fields (order_id, transaction_id, company, support_tier, reseller_id)
 * - batch tracking (batch_id)
 * - new license types: custom, yearly
 * - grace period, renewable flag
 * - metadata, prefix, suspended_at, revoked_at
 * - performance indexes
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            // Note: customer_id is already nullable in SQLite (schema allows NULL)
            // For MySQL production, run: ALTER TABLE licenses MODIFY customer_id BIGINT UNSIGNED NULL;

            // Batch relationship — FK added in migration 000004b after license_batches exists
            $table->unsignedBigInteger('batch_id')->nullable()->after('customer_id');

            // Extended customer / order data
            $table->string('order_id')->nullable()->after('license_key');
            $table->string('transaction_id')->nullable()->after('order_id');
            $table->string('reseller_id')->nullable()->after('transaction_id')
                ->comment('Reseller tag or UUID from external system');
            $table->string('support_tier')->nullable()->after('reseller_id')
                ->comment('standard|priority|enterprise');

            // License type expansion (add yearly, custom to existing enum)
            // We use a raw query for MySQL enum extension to maintain backward compat
            // New values: yearly, custom (monthly, annual, lifetime, trial, floating, multi-device already exist)

            // Grace period & renewability
            $table->unsignedSmallInteger('grace_period_days')->default(0)->after('expires_at');
            $table->boolean('is_renewable')->default(true)->after('grace_period_days');

            // Prefix stored for display/export
            $table->string('key_prefix', 10)->nullable()->after('license_key');

            // Metadata, timestamps for lifecycle
            $table->json('metadata')->nullable()->after('notes');
            $table->timestamp('suspended_at')->nullable()->after('metadata');
            $table->timestamp('revoked_at')->nullable()->after('suspended_at');
            $table->timestamp('first_activated_at')->nullable()->after('revoked_at');

            // Indexes
            $table->index('status');
            $table->index('expires_at');
            $table->index('product_id');
            $table->index('customer_id');
            $table->index('batch_id');
            $table->index('order_id');
            $table->index('reseller_id');
        });

        // Extend the `type` enum to include yearly and custom (SQLite ignores MODIFY, MySQL needs it)
        if (config('database.default') !== 'sqlite') {
            \DB::statement("ALTER TABLE licenses MODIFY COLUMN type ENUM(
                'lifetime','monthly','annual','yearly','trial','floating','multi-device','custom'
            ) NOT NULL DEFAULT 'lifetime'");
        }
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['batch_id']);
            $table->dropIndex(['order_id']);
            $table->dropIndex(['reseller_id']);
            $table->dropColumn([
                'batch_id', 'order_id', 'transaction_id', 'reseller_id',
                'support_tier', 'grace_period_days', 'is_renewable',
                'key_prefix', 'metadata', 'suspended_at', 'revoked_at', 'first_activated_at',
            ]);
        });

        if (config('database.default') !== 'sqlite') {
            \DB::statement("ALTER TABLE licenses MODIFY COLUMN type ENUM(
                'lifetime','monthly','annual','trial','floating','multi-device'
            ) NOT NULL DEFAULT 'lifetime'");
        }
    }
};
