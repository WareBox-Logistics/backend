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
        Schema::create('modell', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand');
            $table->boolean('truck');
            $table->year('year');
            $table->timestamps();

            $table->foreign('brand')->references('id')->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modells');
    }
};
