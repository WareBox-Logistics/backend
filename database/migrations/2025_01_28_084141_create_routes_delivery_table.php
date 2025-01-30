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
        Schema::create('routes_delivery', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery');
            $table->unsignedBigInteger('route');
            $table->boolean('isBackup');
            $table->timestamps();

            $table->foreign('delivery')->references('id')->on('delivery');
            $table->foreign('route')->references('id')->on('route');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes_delivery');
    }
};
