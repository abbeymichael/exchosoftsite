<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extend customers table with advanced CRM fields for customer-linked licensing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('country', 2)->nullable()
                ->comment('ISO 3166-1 alpha-2');
            $table->string('reseller_id')->nullable()
                ->comment('Reseller partner identifier from external system');
            $table->string('external_id')->nullable()
                ->comment('ID from external e-commerce / CRM system');
            $table->json('metadata')->nullable();
            $table->timestamp('archived_at')->nullable();

            // Only index new columns (email already has unique index)
            $table->index('external_id');
            $table->index('reseller_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['external_id']);
            $table->dropIndex(['reseller_id']);
            $table->dropColumn(['country', 'reseller_id', 'external_id', 'metadata', 'archived_at']);
        });
    }
};
