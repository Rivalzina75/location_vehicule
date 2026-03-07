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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // start, end
            $table->timestamp('inspection_date');
            $table->integer('mileage');
            $table->string('fuel_level'); // empty, quarter, half, three_quarters, full
            $table->string('cleanliness'); // dirty, acceptable, clean, very_clean
            $table->boolean('exterior_ok')->default(true);
            $table->boolean('interior_ok')->default(true);
            $table->boolean('tires_ok')->default(true);
            $table->boolean('lights_ok')->default(true);
            $table->boolean('documents_ok')->default(true);
            $table->json('photos')->nullable();
            $table->json('damages')->nullable();
            $table->text('general_notes')->nullable();
            $table->text('damage_notes')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
