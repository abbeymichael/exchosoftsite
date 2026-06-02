<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title')->nullable();
            $table->text('message');

            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();

            $table->boolean('is_active')->default(false);
            $table->boolean('is_dismissible')->default(true);

            $table->unsignedInteger('priority')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_notifications');
    }
};
