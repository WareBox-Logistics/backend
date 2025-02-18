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
        Schema::create('pallet', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('company');
            $table->unsignedBigInteger('warehouse');
            $table->decimal('weight')->check('weight > 0');
            $table->decimal('volume')->check('volume > 0');
            $table->string('status', 50)->check("status IN ('Created', 'Stored', 'In Transit', 'Delivered')");

            $table->foreign('company')->references('id')->on('company');
            $table->foreign('warehouse')->references('id')->on('warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pallet');
    }
};
