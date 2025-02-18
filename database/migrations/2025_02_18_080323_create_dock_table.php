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
        Schema::create('dock', function (Blueprint $table) {
            $table->id();
            $table->string('status', 50)->check("status IN ('Available', 'Occupied', 'Maintenance')");
            $table->string('type', 50)->check("type IN ('Loading', 'Unloading')");
            $table->unsignedBigInteger('warehouse');
            $table->timestamps();

            $table->foreign('warehouse')->references('id')->on('warehouse')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dock');
    }
};
