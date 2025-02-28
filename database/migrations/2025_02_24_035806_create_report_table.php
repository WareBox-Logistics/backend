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
        Schema::create('report', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route');
            $table->string('ubication');
            $table->boolean('issue');
            $table->string('description');
            $table->unsignedBigInteger('driver');
            $table->timestamps();

            $table->foreign('route')->references('id')->on('route');
            $table->foreign('driver')->references('id')->on('employee');
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
