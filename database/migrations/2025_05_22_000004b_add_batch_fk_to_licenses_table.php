<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add the batch_id foreign key constraint to the licenses table.
 * Runs AFTER create_license_batches_table (000004).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->foreign('batch_id')
                ->references('id')
                ->on('license_batches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
        });
    }
};
