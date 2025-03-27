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
        // Check and rename columns if needed
        if (Schema::hasColumn('delivery', 'truck_id') && !Schema::hasColumn('delivery', 'truck')) {
            Schema::table('delivery', function (Blueprint $table) {
                $table->renameColumn('truck_id', 'truck');
            });
        }
        
        if (Schema::hasColumn('delivery', 'trailer_id') && !Schema::hasColumn('delivery', 'trailer')) {
            Schema::table('delivery', function (Blueprint $table) {
                $table->renameColumn('trailer_id', 'trailer');
            });
        }

        // Add foreign keys if they don't exist
        Schema::table('delivery', function (Blueprint $table) {
            $foreignKeys = collect(DB::select("
                SELECT conname, conrelid::regclass AS table_name, 
                pg_get_constraintdef(oid) AS constraint_def 
                FROM pg_constraint 
                WHERE conrelid = 'delivery'::regclass
                AND contype = 'f'
            "));

            // Check for truck foreign key
            $hasTruckFK = $foreignKeys->contains(function ($fk) {
                return str_contains($fk->constraint_def, 'FOREIGN KEY (truck) REFERENCES vehicles(id)');
            });

            if (!$hasTruckFK && Schema::hasColumn('delivery', 'truck')) {
                $table->foreign('truck')->references('id')->on('vehicles');
            }

            // Check for trailer foreign key
            $hasTrailerFK = $foreignKeys->contains(function ($fk) {
                return str_contains($fk->constraint_def, 'FOREIGN KEY (trailer) REFERENCES vehicles(id)');
            });

            if (!$hasTrailerFK && Schema::hasColumn('delivery', 'trailer')) {
                $table->foreign('trailer')->references('id')->on('vehicles');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery', function (Blueprint $table) {
            // Drop foreign keys if they exist
            $foreignKeys = collect(DB::select("
                SELECT conname, conrelid::regclass AS table_name, 
                pg_get_constraintdef(oid) AS constraint_def 
                FROM pg_constraint 
                WHERE conrelid = 'delivery'::regclass
                AND contype = 'f'
            "));

            // Drop truck foreign key
            $truckFK = $foreignKeys->first(function ($fk) {
                return str_contains($fk->constraint_def, 'FOREIGN KEY (truck) REFERENCES vehicles(id)');
            });

            if ($truckFK) {
                $table->dropForeign([$truckFK->conname]);
            }

            // Drop trailer foreign key
            $trailerFK = $foreignKeys->first(function ($fk) {
                return str_contains($fk->constraint_def, 'FOREIGN KEY (trailer) REFERENCES vehicles(id)');
            });

            if ($trailerFK) {
                $table->dropForeign([$trailerFK->conname]);
            }

            // Rename columns back if they were changed
            if (Schema::hasColumn('delivery', 'truck') && !Schema::hasColumn('delivery', 'truck_id')) {
                $table->renameColumn('truck', 'truck_id');
            }
            
            if (Schema::hasColumn('delivery', 'trailer') && !Schema::hasColumn('delivery', 'trailer_id')) {
                $table->renameColumn('trailer', 'trailer_id');
            }
        });
    }
};