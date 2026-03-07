<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days');
            $table->decimal('base_price', 10, 2);
            $table->decimal('options_price', 10, 2)->default(0);
            $table->decimal('insurance_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->boolean('child_seat')->default(false);
            $table->boolean('gps')->default(false);
            $table->boolean('additional_driver')->default(false);
            $table->boolean('insurance_full')->default(false);
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, active, completed, cancelled, late
            $table->string('payment_status')->default('pending'); // pending, completed, refunded
            $table->string('confirmation_code')->unique();
            $table->boolean('start_inspection_done')->default(false);
            $table->boolean('end_inspection_done')->default(false);
            $table->integer('mileage_start')->nullable();
            $table->integer('mileage_end')->nullable();
            $table->decimal('damage_cost', 10, 2)->default(0);
            $table->decimal('late_penalty', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['vehicle_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
