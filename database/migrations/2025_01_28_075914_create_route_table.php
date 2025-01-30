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
        Schema::create('route', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origin');
            $table->unsignedBigInteger('destination');
            //if possible this is where we would implement the stops, how? idk
            $table->timestamps();

            $table->foreign('origin')->references('id')->on('location');
            $table->foreign('destination')->references('id')->on('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route');
    }
};
