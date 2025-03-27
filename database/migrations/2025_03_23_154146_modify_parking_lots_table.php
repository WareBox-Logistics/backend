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
        Schema::table('parking_lots', function (Blueprint $table) {
            $table->integer('rows');
            $table->integer('columns');
            $table->dropColumn('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parkingLot', function (Blueprint $table) {
            $table->dropColumn('rows');
            $table->dropColumn('columns');
            $table->integer('capacity');
        });
    }
};
