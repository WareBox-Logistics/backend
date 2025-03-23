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
            $table->unsignedBigInteger('brand_id');
            $table->string('name'); 
            $table->boolean('is_truck')->default(false);
            $table->boolean('is_trailer')->default(false);
            $table->year('year');
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade'); // Added onDelete('cascade')
            $table->unique(['brand_id', 'name', 'year']); // Added unique constraint for brand, name, and year
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
