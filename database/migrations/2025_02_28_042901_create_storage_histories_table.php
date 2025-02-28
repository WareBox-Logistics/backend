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
        Schema::create('storage_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pallet');
            $table->unsignedBigInteger('rack');
            $table->unsignedBigInteger('warehouse');
            $table->string('position', 4);
            $table->integer('level');
            $table->timestamp('stored_at');
            $table->timestamp('removed_at');
            $table->timestamps();

            $table->foreign('pallet')->references('id')->on('pallet');
            $table->foreign('rack')->references('id')->on('rack');
            $table->foreign('warehouse')->references('id')->on('warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_histories');
    }
};
