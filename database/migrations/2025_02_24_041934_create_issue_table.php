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
        Schema::create('issue', function (Blueprint $table) {
            $table->id();
            $table->string('status')->check("status IN  ('WIP','DONE','WAIT')");
            $table->string('description');
            $table->unsignedBigInteger('report');
            $table->unsignedBigInteger('operator');
            $table->boolean('support');
            $table->timestamps();

            $table->foreign('report')->references('id')->on('report');
            $table->foreign('operator')->references('id')->on('employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
