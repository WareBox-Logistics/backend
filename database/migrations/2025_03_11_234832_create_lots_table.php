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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('spot_code');
            $table->unsignedBigInteger('parking_lot_id');
            $table->unsignedBigInteger('vehicle_id')->nullable()->unique(); // Foreign key to vehicles, can be null if lot is empty
            $table->boolean('is_occupied')->default(false);
            $table->string('allowed_type', 20)->check("allowed_type IN ('trailer','semi_truck','both')");
            $table->timestamps();

            $table->unique(['spot_code', 'parking_lot_id']);
            $table->foreign('parking_lot_id')->references('id')->on('parking_lots')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null'); // If a vehicle is deleted, the lot becomes unoccupied
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
