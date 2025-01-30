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
        Schema::create('truck', function (Blueprint $table) {
            $table->id();
            $table->string('plates')->unique();
            $table->string('vin')->unique();
            $table->enum('brand', ['Freightliner', 'Kenworth', 'Peterbilt', 'Volvo', 'Mack', 'International', 'Western Star', 'Sterling', 'Hino', 'Isuzu']);
            $table->enum('model', ['Cascadia', 'T680', '579', 'VNL', 'Anthem', 'LT', '4900', 'Acterra', '338', 'N-Series']);
            $table->unsignedBigInteger('driver');
            $table->timestamps();

            $table->foreign('driver')->references('id')->on('employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck');
    }
};
