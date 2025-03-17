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
            $table->unsignedBigInteger('model');
            $table->decimal('volume', 10, 2)->nullable();
            $table->unsignedBigInteger('driver');
            $table->timestamps();

            $table->foreign('model')->references('id')->on('modell');
            $table->foreign('driver')->references('id')->on('employee');
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
