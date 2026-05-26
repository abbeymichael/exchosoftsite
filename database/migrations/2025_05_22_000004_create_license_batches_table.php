<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_batches', function (Blueprint $table) {
            $table->id();

            // Ownership
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // Batch identity
            $table->string('label')->comment('Human-readable batch name');
            $table->string('batch_code')->unique()->comment('Unique short code e.g. BATCH-2025-001');

            // Generation params
            $table->string('key_prefix', 10)->default('EXCL')
                ->comment('Key prefix used when generating e.g. EXCL-XXXX-XXXX-XXXX');
            $table->unsignedInteger('quantity');
            $table->string('reseller_tag')->nullable();

            // License template params
            $table->enum('license_type', ['lifetime', 'monthly', 'annual', 'yearly', 'trial', 'custom'])
                ->default('lifetime');
            $table->enum('edition', ['standard', 'professional', 'enterprise', 'trial'])
                ->default('standard');
            $table->unsignedSmallInteger('max_activations')->default(1);
            $table->timestamp('expires_at')->nullable()->comment('Common expiry for all keys in batch; null = lifetime');
            $table->unsignedSmallInteger('duration_days')->nullable()
                ->comment('Used when expires_at is null and type is not lifetime');

            // Tracking
            $table->unsignedInteger('total_generated')->default(0);
            $table->unsignedInteger('total_used')->default(0);
            $table->unsignedInteger('total_revoked')->default(0);

            // Status
            $table->enum('status', ['active', 'expired', 'revoked', 'archived'])->default('active');
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('status');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_batches');
    }
};
