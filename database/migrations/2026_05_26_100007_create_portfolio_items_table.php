<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('category')->default('software'); // software, web, mobile, design, consulting
            $table->json('tech_stack')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_industry')->nullable();
            $table->string('project_url')->nullable();
            $table->string('github_url')->nullable();
            $table->date('completed_at')->nullable();
            $table->string('duration')->nullable(); // e.g. "3 months"
            $table->json('highlights')->nullable(); // key achievements
            $table->string('status')->default('published'); // draft, published, archived
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
