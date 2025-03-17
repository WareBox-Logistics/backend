<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parking_assigments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery');
            $table->unsignedBigInteger('lot');
            $table->string('status',20)->check("status IN ('Waiting','Arrived','Gone','Canceled')");
            $table->timestamp('arrival')->nullable();
            $table->timestamp('exit')->nullable();
            $table->timestamps();

            $table->foreign('delivery')->references('id')->on('delivery');
            $table->foreign('lot')->references('id')->on('lots');
        });

        DB::statement('
            CREATE UNIQUE INDEX only_one_active_assignment_per_lot 
            ON parking_assigments (lot) 
            WHERE exit IS NULL
        ');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_assigments');
    }
};
