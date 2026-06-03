<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->enum('type', ['individual', 'company'])->default('individual');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            // Extended CRM fields
            $table->string('country', 2)->nullable()->comment('ISO 3166-1 alpha-2');
            $table->string('reseller_id')->nullable()->comment('Reseller partner identifier');
            $table->string('external_id')->nullable()->comment('ID from external e-commerce / CRM system');
            $table->json('metadata')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            $table->index('external_id');
            $table->index('reseller_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
