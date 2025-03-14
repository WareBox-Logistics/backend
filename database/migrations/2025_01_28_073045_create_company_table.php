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
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rfc');
            $table->string('email');
            $table->string('phone');
            $table->unsignedBigInteger('service');

            $table->timestamps();

            $table->foreign('service')->references('id')->on('service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company');
    }
};
