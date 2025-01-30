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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse');
            $table->unsignedBigInteger('product');
            $table->integer('qty');
            //maybe box should go here?
            $table->timestamps();

            $table->foreign('warehouse')->references('id')->on('warehouse');
            $table->foreign('product')->references('id')->on('product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
