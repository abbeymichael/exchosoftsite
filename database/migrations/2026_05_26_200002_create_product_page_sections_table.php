<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('product_code'); // washops, churchops, etc.
            $table->string('section_key');  // hero, features, roi, etc.
            $table->string('label')->nullable();
            $table->longText('content')->nullable();       // markdown or text
            $table->json('data')->nullable();              // structured data (features, stats, etc.)
            $table->string('type')->default('markdown');   // markdown, json, text
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['product_code', 'section_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_page_sections');
    }
};
