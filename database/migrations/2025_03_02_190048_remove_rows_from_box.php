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
        Schema::table('box_inventory', function (Blueprint $table) {
            $table->dropColumn(['height', 'width', 'depth']);
        });

        Schema::dropIfExists('inventory');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('box', function (Blueprint $table) {
            $table->integer('height');
            $table->integer('width');
            $table->integer('dept');
        });
    }
};
