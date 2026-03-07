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
        if (Schema::hasTable('vehicles')) {
            return;
        }

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('type'); // car, suv, van, motorcycle, scooter, citycar, luxury, convertible
            $table->integer('year');
            $table->string('registration_number')->unique();
            $table->string('transmission'); // manual, automatic
            $table->string('fuel_type'); // gasoline, diesel, electric, hybrid
            $table->integer('seats');
            $table->integer('doors')->nullable();
            $table->integer('engine_power')->nullable();
            $table->decimal('fuel_consumption', 5, 2)->nullable();
            $table->integer('trunk_capacity')->nullable();
            $table->decimal('price_per_day', 8, 2);
            $table->decimal('price_per_week', 8, 2)->nullable();
            $table->decimal('price_per_month', 8, 2)->nullable();
            $table->decimal('deposit', 8, 2)->default(0);
            $table->integer('mileage')->default(0);
            $table->boolean('gps_available')->default(false);
            $table->boolean('child_seat_available')->default(false);
            $table->boolean('bluetooth')->default(false);
            $table->boolean('air_conditioning')->default(false);
            $table->boolean('cruise_control')->default(false);
            $table->boolean('parking_sensors')->default(false);
            $table->boolean('backup_camera')->default(false);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('status')->default('available'); // available, rented, maintenance
            $table->decimal('rating', 3, 1)->nullable();
            $table->integer('reviews_count')->default(0);
            $table->integer('rental_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
