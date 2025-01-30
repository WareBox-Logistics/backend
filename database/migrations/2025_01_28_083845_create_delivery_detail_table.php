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
        Schema::create('delivery_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery');
            $table->unsignedBigInteger('product');
            $table->integer('qty');
            $table->timestamps();

            $table->foreign('delivery')->references('id')->on('delivery');
            $table->foreign('product')->references('id')->on('product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_detail');
    }
};
