<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Product Plans define the available billing options for each product.
     * A product can have multiple plans: Monthly, Yearly, Lifetime, or custom.
     * Each plan has its own price, duration, and behaviour overrides.
     */
    public function up(): void
    {
        Schema::create('product_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();

            // Display
            $table->string('name');                         // "Monthly", "Yearly", "Lifetime", "Custom"
            $table->string('slug');                         // monthly | yearly | lifetime | custom
            $table->text('description')->nullable();
            $table->string('form_factor')->default('standalone')->after('product_id');

            // Pricing — each plan has its own price
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            // Duration — 0 means lifetime / perpetual
            $table->unsignedInteger('duration_days')->default(0);

            // Trial
            $table->unsignedInteger('trial_days')->default(0);        // 0 = no trial on this plan
            $table->boolean('is_trial_eligible')->default(true);       // can plan be reached after trial?

            // Behaviour
            $table->boolean('is_renewable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            // Caps — inherited from product unless overridden here
            $table->unsignedInteger('max_activations')->nullable();    // null = use product default
            $table->unsignedInteger('offline_ttl_hours')->nullable();  // null = use product default
            $table->unsignedInteger('grace_period_days')->nullable();  // null = use product default

            $table->timestamps();

            $table->unique(['product_id', 'slug']);
            $table->index(['product_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_plans');
    }
};
