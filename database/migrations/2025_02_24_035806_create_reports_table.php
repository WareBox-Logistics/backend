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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route');
            $table->foreign('route')->references('id')->on('route');
            $table->string('ubication');
            $table->boolean('issue');
            $table->string('description');
            $table->unsignedBigInteger('driver');
            $table->foreign('driver')->references('id')->on('employee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
