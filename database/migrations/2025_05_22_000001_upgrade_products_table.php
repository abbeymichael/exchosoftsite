<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Upgrade the products table with advanced licensing fields:
 * app_identifier, secret_key, version, support_email, webhook_url,
 * metadata, default_duration_days, max_devices, created_by, archived_at
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // App identity & security
            $table->string('app_identifier')->nullable()->unique()
                ->comment('Unique reverse-DNS style identifier e.g. com.exchosoft.appname');
            $table->string('secret_key', 64)->nullable()
                ->comment('HMAC secret for webhook & payload signing');

            // Versioning alias
            $table->string('version')->nullable()
                ->comment('Current published version (alias to current_version)');

            // Contact & webhook
            $table->string('support_email')->nullable();
            $table->string('webhook_url')->nullable();

            // Licensing defaults
            $table->unsignedSmallInteger('max_devices')->default(1)
                ->comment('Default device limit for new licenses of this product');
            $table->unsignedSmallInteger('default_duration_days')->nullable()
                ->comment('0 = lifetime; null = inherit from plan');

            // Metadata & config JSON
            $table->json('metadata')->nullable();

            // Ownership & lifecycle
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('archived_at')->nullable();
        });

        // Add FK for created_by after column exists
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'app_identifier', 'secret_key', 'version',
                'support_email', 'webhook_url',
                'max_devices', 'default_duration_days',
                'metadata', 'created_by', 'archived_at',
            ]);
        });
    }
};
