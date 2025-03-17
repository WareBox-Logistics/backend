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
            $table->unsignedBigInteger('parking_lot');
            $table->boolean('is_occupied');
            $table->string('allowed_type',20)->check("allowed_type IN ('trailer','truck','both')");
            $table->timestamps();

            $table->unique(['spot_code','parking_lot']);
            $table->foreign('parking_lot')->references('id')->on('parking_lots');
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
