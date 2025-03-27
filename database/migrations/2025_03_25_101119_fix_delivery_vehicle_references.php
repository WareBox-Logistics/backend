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
        Schema::table('delivery', function (Blueprint $table) {
            // Check if the constraint exists before trying to drop it
            $constraints = DB::select("
                SELECT conname
                FROM pg_constraint
                WHERE conrelid = 'delivery'::regclass
                AND contype = 'f'
                AND conname = 'delivery_truck_foreign'
            ");
            
            if (count($constraints) > 0) {
                $table->dropForeign(['truck']);
            }
        });

        // Recreate the correct foreign key
        Schema::table('delivery', function (Blueprint $table) {
            $table->foreign('truck')
                  ->references('id')
                  ->on('vehicles')  // Changed from 'truck' to 'vehicles'
                  ->onDelete('restrict');
        });

        // Repeat for trailer if needed
        Schema::table('delivery', function (Blueprint $table) {
            $constraints = DB::select("
                SELECT conname
                FROM pg_constraint
                WHERE conrelid = 'delivery'::regclass
                AND contype = 'f'
                AND conname = 'delivery_trailer_foreign'
            ");
            
            if (count($constraints) > 0) {
                $table->dropForeign(['trailer']);
            }
            
            $table->foreign('trailer')
                  ->references('id')
                  ->on('vehicles')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery', function (Blueprint $table) {
            $table->dropForeign(['truck']);
            $table->dropForeign(['trailer']);
        });
    }
};
