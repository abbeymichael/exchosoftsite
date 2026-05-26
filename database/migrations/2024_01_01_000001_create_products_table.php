<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('product_code')->unique();
            $table->enum('platform', ['desktop', 'saas', 'hybrid', 'offline-first'])->default('desktop');
            $table->string('current_version')->default('1.0.0');
            $table->enum('pricing_type', ['lifetime', 'subscription', 'trial', 'free'])->default('lifetime');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
