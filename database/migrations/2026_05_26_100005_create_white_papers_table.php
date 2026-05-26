<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('white_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('file_path')->nullable(); // PDF download
            $table->string('category')->default('general'); // product, technology, industry, research
            $table->json('tags')->nullable();
            $table->foreignId('shop_product_id')->nullable()->constrained()->nullOnDelete(); // linked product
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('is_gated')->default(true); // requires email/registration
            $table->timestamp('published_at')->nullable();
            $table->integer('downloads')->default(0);
            $table->integer('views')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('white_papers');
    }
};
