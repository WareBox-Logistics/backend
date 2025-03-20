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
        Schema::create('rack', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse');
            $table->string('section', 255);
            $table->string('status', 50)->check("status IN ('Available', 'Full')");
            $table->decimal('capacity_volume')->check('capacity_volume > 0');
            $table->decimal('used_volume')->check('used_volume <= capacity_volume');
            $table->decimal('capacity_weight')->check('capacity_weight > 0');
            $table->decimal('used_weight')->check('used_weight <= capacity_weight');            
            $table->timestamps();

            $table->foreign('warehouse')->references('id')->on('warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rack');
    }
};
