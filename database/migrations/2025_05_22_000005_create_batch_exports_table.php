<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('license_batches')->cascadeOnDelete();
            $table->foreignId('exported_by')->constrained('users')->cascadeOnDelete();
            $table->string('filename');
            $table->enum('format', ['csv', 'json', 'txt'])->default('csv');
            $table->unsignedInteger('record_count')->default(0);
            $table->string('storage_path')->nullable()->comment('Relative path in storage/app');
            $table->timestamp('expires_at')->nullable()->comment('Auto-delete download after this time');
            $table->timestamps();

            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_exports');
    }
};
