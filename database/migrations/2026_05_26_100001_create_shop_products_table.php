<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->longText('full_description')->nullable();
            $table->string('category')->default('software'); // software, template, course, service
            $table->string('product_type')->default('digital'); // digital, physical, service
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('currency', 10)->default('GHS');
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('features')->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('download_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('documentation_url')->nullable();
            $table->string('version')->nullable();
            $table->string('platform')->nullable(); // windows, web, cross-platform
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_license')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('sales_count')->default(0);
            $table->string('linked_product_code')->nullable(); // links to products table
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_products');
    }
};
