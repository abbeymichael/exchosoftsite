<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->longText('snapshot')->nullable();   // full JSON snapshot of the page row
            $table->string('changed_by')->nullable();   // admin name/email
            $table->string('note')->nullable();          // e.g. "Updated privacy section 3"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_versions');
    }
};
