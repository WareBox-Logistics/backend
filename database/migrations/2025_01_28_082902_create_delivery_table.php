<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('truck');
            $table->unsignedBigInteger('trailer');
            $table->unsignedBigInteger('company');
            $table->unsignedBigInteger('created_by');
            $table->enum('status', ['Pending','Docking','Loading','Delivering','Emptying']);
            $table->unsignedBigInteger('origin');
            $table->unsignedBigInteger('destination');
            $table->dateTime('date_created')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('finished_date')->nullable();
            $table->timestamps();

            $table->foreign('truck')->references('id')->on('vehicle');
            $table->foreign('trailer')->references('id')->on('vehicle');
            $table->foreign('company')->references('id')->on('company');
            $table->foreign('origin')->references('id')->on('location');
            $table->foreign('destination')->references('id')->on('location');
            $table->foreign('created_by')->references('id')->on('employee');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery');
    }
};
