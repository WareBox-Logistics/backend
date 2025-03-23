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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plates')->unique();
            $table->string('vin')->unique();
            $table->unsignedBigInteger('model_id');
            $table->decimal('volume', 10, 2)->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->enum('type', ['semi_truck', 'trailer'])->default('semi_truck'); // Added a 'type' enum to distinguish between semi-trucks and trailers
            $table->timestamps();

            $table->foreign('model_id')->references('id')->on('modell')->onDelete('cascade'); 
            $table->foreign('driver_id')->references('id')->on('employee')->onDelete('set null'); 
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
