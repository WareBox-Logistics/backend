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
        Schema::create('parking_lots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('warehouse_id');
            $table->integer('capacity');
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouse')->onDelete('cascade'); // Added onDelete('cascade')
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_lots');
    }
};
