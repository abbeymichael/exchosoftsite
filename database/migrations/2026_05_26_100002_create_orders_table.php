<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_user_id')->nullable()->constrained('users')->nullOnDelete();
            // Guest checkout
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_company')->nullable();
            // Order totals
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency', 10)->default('GHS');
            // Status
            $table->string('status')->default('pending'); // pending, paid, processing, completed, cancelled, refunded
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, failed, refunded
            $table->string('payment_method')->nullable(); // paystack, momo, bank_transfer, manual
            $table->string('payment_reference')->nullable();
            $table->json('payment_meta')->nullable();
            $table->timestamp('paid_at')->nullable();
            // Fulfillment
            $table->string('fulfillment_status')->default('pending'); // pending, processing, fulfilled
            $table->timestamp('fulfilled_at')->nullable();
            // Coupon
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 12, 2)->default(0);
            // Notes
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_version')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 12, 2);
            $table->string('license_key_issued')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
