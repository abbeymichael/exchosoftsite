<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_product_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('shop_order_id')->nullable()->after('shop_product_id');
            $table->string('buyer_email')->nullable()->after('shop_order_id');
            $table->string('buyer_name')->nullable()->after('buyer_email');
        });
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['shop_product_id', 'shop_order_id', 'buyer_email', 'buyer_name']);
        });
    }
};
