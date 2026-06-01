<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('product_code')->nullable()->unique();
            $table->string('platform')->nullable(); // desktop|saas|hybrid|offline-first
            $table->string('current_version')->nullable();
            $table->string('pricing_type')->nullable(); // one-time|subscription|free
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);

            // --- Licensing ---
            $table->string('app_identifier')->nullable()->unique();
            $table->string('secret_key', 64)->nullable();
            $table->string('support_email')->nullable();
            $table->string('webhook_url')->nullable();
            $table->unsignedSmallInteger('max_devices')->default(1);
            $table->unsignedSmallInteger('default_duration_days')->nullable(); // null=inherit, 0=lifetime
            $table->string('min_app_version')->nullable();
            $table->string('max_app_version')->nullable();
            $table->unsignedSmallInteger('offline_ttl_hours')->nullable();
            $table->unsignedSmallInteger('grace_period_days')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('archived_at')->nullable();

            // --- Shop ---
            $table->string('tagline')->nullable();
            $table->longText('full_description')->nullable();
            $table->string('category')->nullable();
            $table->string('product_type')->nullable()->default('software');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('features')->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('documentation_url')->nullable();
            $table->string('download_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('sales_count')->default(0);

            // --- Ownership ---
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['is_published', 'is_featured']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
