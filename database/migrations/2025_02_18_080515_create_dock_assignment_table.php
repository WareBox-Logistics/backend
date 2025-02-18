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
        Schema::create('dock_assignment', function (Blueprint $table) {
            $table->unsignedBigInteger('dock');
            $table->unsignedBigInteger('truck');
            $table->string('status', 50)->check("status IN ('Scheduled', 'In Progress', 'Completed', 'Cancelled')");
            $table->timestamp('scheduled_time')->nullable();
            $table->timestamps();

            $table->primary(['dock', 'truck']);

            $table->foreign('dock')->references('id')->on('dock')->cascadeOnDelete();
            $table->foreign('truck')->references('id')->on('truck')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dock_assignment');
    }
};
