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
        Schema::table('delivery', function (Blueprint $table) {
            // Rename the existing 'truck' column to 'truck_id'
            $table->renameColumn('truck', 'truck_id');
            $table->foreign('truck_id')->references('id')->on('vehicles')->onDelete('set null');

            // Rename the existing 'trailer' column to 'trailer_id'
            $table->renameColumn('trailer', 'trailer_id');
            $table->foreign('trailer_id')->references('id')->on('vehicles')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery', function (Blueprint $table) {
            $table->dropForeign(['truck_id']);
            $table->renameColumn('truck_id', 'truck');

            $table->dropForeign(['trailer_id']);
            $table->renameColumn('trailer_id', 'trailer');
        });
    }
};
