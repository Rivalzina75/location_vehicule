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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_brand', 20);       // visa, mastercard, amex, etc.
            $table->string('card_last_four', 4);
            $table->string('card_holder_name');
            $table->string('expiry_month', 2);
            $table->string('expiry_year', 4);
            $table->boolean('is_default')->default(false);
            $table->string('stripe_payment_method_id')->nullable(); // For future Stripe integration
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
