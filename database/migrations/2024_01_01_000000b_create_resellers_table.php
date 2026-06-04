<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resellers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Linked to a user account — reseller logs in as a normal user
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            $table->string('company_name')->nullable();

            // Unique code used in referral URLs: ?ref=CODE and at checkout
            $table->string('reseller_code', 32)->unique();

            // wholesale  = they buy license batches from you at a discount, resell at own price
            // referral   = customer buys from you, reseller earns a % commission
            // both       = supports both modes
            $table->enum('type', ['wholesale', 'referral', 'both'])->default('referral');

            // Referral commission rate (percentage, e.g. 20.00 = 20%)
            $table->decimal('commission_rate', 5, 2)->default(0);

            // Wholesale discount rate (percentage off product price when buying batches)
            $table->decimal('discount_rate', 5, 2)->default(0);

            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');

            // Running totals — updated on each commission record creation / payout
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);    // total_earned - total_paid

            // Payout preferences
            $table->string('payout_method')->nullable();      // bank | paypal | crypto | manual
            $table->json('payout_details')->nullable();        // account details (encrypt at rest)

            $table->string('currency', 3)->default('USD');
            $table->decimal('minimum_payout', 10, 2)->default(50.00);

            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'type']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resellers');
    }
};
