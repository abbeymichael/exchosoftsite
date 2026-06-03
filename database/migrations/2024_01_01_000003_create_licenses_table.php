<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Core relations
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('plan_id')->nullable()->constrained('product_plans')->nullOnDelete();
            $table->foreignUuid('reseller_id')->nullable()->constrained('resellers')->nullOnDelete();

            // Batch & Order (added later but included here for clean schema)
            $table->string('batch_id')->nullable()->index(); // FK added after license_batches is created
            $table->string('order_id')->nullable()->index(); // FK added after orders is created
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->foreign('issued_by')->references('id')->on('users')->nullOnDelete();

            // License identity
            $table->string('license_key')->unique();
            $table->string('key_prefix', 10)->default('EXCL')->nullable();
            $table->enum('edition', ['standard', 'professional', 'enterprise', 'trial'])->default('standard');
            $table->enum('type', ['lifetime', 'monthly', 'annual', 'trial', 'floating', 'multi-device'])->default('lifetime');

            // Activation caps
            $table->integer('max_activations')->default(1);
            $table->integer('current_activations')->default(0);

            // Timestamps
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('first_activated_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Status
            $table->enum('status', ['inactive', 'active', 'expired', 'suspended', 'revoked', 'trial'])->default('active');

            // Enterprise / advanced fields
            $table->json('features')->nullable()->comment('Array of feature-flag strings this license is entitled to');
            $table->string('revocation_checksum', 64)->nullable()->comment('SHA-256 hash; changes on any revocation event');
            $table->string('min_app_version', 32)->nullable();
            $table->string('max_app_version', 32)->nullable();

            // Overrides from product/plan
            $table->integer('grace_period_days')->nullable();
            $table->boolean('is_renewable')->default(true);

            $table->string('support_tier')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('product_id');
            $table->index('customer_id');
            $table->index('reseller_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
