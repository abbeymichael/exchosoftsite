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

            // Buyer — either a registered user or a guest
            $table->unsignedBigInteger('customer_user_id')->nullable();
            $table->foreign('customer_user_id')->references('id')->on('users')->nullOnDelete();

            // Reseller attribution
            $table->foreignUuid('reseller_id')->nullable()->constrained('resellers')->nullOnDelete();
            $table->string('reseller_code_used', 32)->nullable();
            $table->decimal('commission_rate_snapshot', 5, 2)->nullable();

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
            $table->string('currency', 10)->default('USD');

            // Status
            $table->string('status')->default('pending');         // pending, paid, processing, completed, cancelled, refunded
            $table->string('payment_status')->default('unpaid');  // unpaid, paid, failed, refunded
            $table->string('payment_method')->nullable();          // paystack, momo, bank_transfer, manual, stripe, paypal
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

            $table->index('order_number');
            $table->index('customer_user_id');
            $table->index('reseller_id');
            $table->index('status');
            $table->index('payment_status');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignUuid('plan_id')->nullable()->constrained('product_plans')->nullOnDelete();
            $table->foreignUuid('license_id')->nullable()->constrained('licenses')->nullOnDelete();

            $table->string('product_name');
            $table->string('plan_name')->nullable();              // snapshot of plan name at time of purchase
            $table->string('product_version')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 12, 2);
            $table->string('license_key_issued')->nullable();

            $table->timestamps();

            $table->index('product_id');
            $table->index('plan_id');
            $table->index('license_id');
        });

        // Now that orders exists, add the order_id FK to licenses
        Schema::table('licenses', function (Blueprint $table) {
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
