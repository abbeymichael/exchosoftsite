<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('reseller_id')->constrained()->cascadeOnDelete();

            // Only one of these will be set depending on type:
            // referral → order_id + license_id
            // wholesale → batch_id (the batch purchase is the sale)
            $table->foreignUuid('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('license_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('batch_id')
                  ->nullable()
                  ->constrained('license_batches')
                  ->nullOnDelete();
            $table->foreignUuid('payment_id')->nullable()->constrained()->nullOnDelete();

            // wholesale_margin = reseller bought batch at discount, margin is implicit
            // referral_commission = % of customer's payment owed to reseller
            $table->enum('type', ['wholesale_margin', 'referral_commission']);

            // Snapshot everything at time of sale — changing rates later won't rewrite history
            $table->decimal('sale_amount', 10, 2);               // full sale price paid
            $table->decimal('commission_rate_snapshot', 5, 2);   // rate applied
            $table->decimal('commission_amount', 10, 2);          // actual amount owed
            $table->string('currency', 3)->default('USD');

            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');

            // Set when included in a payout batch
            $table->foreignUuid('payout_id')
                  ->nullable()
                  ->constrained('reseller_payouts')
                  ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['reseller_id', 'status']);
            $table->index(['reseller_id', 'type']);
            $table->index('payout_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_commissions');
    }
};
