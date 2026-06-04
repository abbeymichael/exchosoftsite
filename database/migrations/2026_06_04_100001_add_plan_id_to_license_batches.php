<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_batches', function (Blueprint $table) {
            // Link batch to a product plan so type/edition/expiry are plan-driven
            $table->foreignUuid('plan_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_plans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('license_batches', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
    }
};
