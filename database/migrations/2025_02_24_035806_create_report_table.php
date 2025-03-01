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
            $table->string('ubication');
            $table->unsignedBigInteger('problem');
            $table->boolean('issue');
            $table->string('description');
            $table->unsignedBigInteger('driver');
            $table->timestamps();

            $table->foreign('problem')->references('id')->on('problem');
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
