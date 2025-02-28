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
        Schema::create('support', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->unsignedBigInteger('issue');
            $table->string('status')->check("status IN  ('WIP','DONE','WAIT')");
            $table->unsignedBigInteger('operator');
            $table->timestamps();

            $table->foreign('issue')->references('id')->on('issue');
            $table->foreign('operator')->references('id')->on('employee');
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
