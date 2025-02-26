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
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->unsignedBigInteger('issue');
            $table->foreign('issue')->references('id')->on('issues');
            $table->string('status')->check("status IN  ('WIP','DONE','WAIT')");
            $table->unsignedBigInteger('operator');
            $table->foreign('operator')->references('id')->on('employee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supports');
    }
};
