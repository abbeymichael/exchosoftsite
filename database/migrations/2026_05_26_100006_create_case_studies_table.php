<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('client_name');
            $table->string('client_industry')->nullable();
            $table->string('client_logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('challenge')->nullable();
            $table->text('solution')->nullable();
            $table->text('results')->nullable();
            $table->json('metrics')->nullable(); // [{"label":"Revenue Increase","value":"45%"}]
            $table->longText('content')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('shop_product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};
