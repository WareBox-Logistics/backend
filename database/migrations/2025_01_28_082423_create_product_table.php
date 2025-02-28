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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('sku');
            $table->decimal('price', 10, 2);
            $table->string('image');
            $table->unsignedBigInteger('company');
            $table->unsignedBigInteger('category');
            $table->timestamps();

            $table->foreign('company')->references('id')->on('company');
            $table->foreign('category')->references('id')->on('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
