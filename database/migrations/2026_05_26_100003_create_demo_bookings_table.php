<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('customer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->foreignId('shop_product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name')->nullable(); // for display
            $table->string('demo_type')->default('online'); // online, onsite
            $table->date('preferred_date');
            $table->string('preferred_time')->nullable(); // e.g. "10:00 AM"
            $table->string('timezone')->default('Africa/Accra');
            $table->integer('attendees')->default(1);
            $table->text('requirements')->nullable();
            $table->text('message')->nullable();
            // Status
            $table->string('status')->default('pending'); // pending, confirmed, rescheduled, completed, cancelled, no_show
            $table->timestamp('confirmed_at')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->string('confirmed_time')->nullable();
            $table->string('meeting_link')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_bookings');
    }
};
