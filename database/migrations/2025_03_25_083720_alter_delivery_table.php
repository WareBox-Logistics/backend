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
            $table->dropForeign(['origin']);
            $table->dropForeign(['destination']);
            $table->dropForeign(['created_by']);
        });

        // Rename columns
        Schema::table('delivery', function (Blueprint $table) {
            $table->renameColumn('date_created', 'shipping_date');
            $table->renameColumn('finished_date', 'completed_date');
        });

        // Add new columns
        Schema::table('delivery', function (Blueprint $table) {
            $table->enum('type', [
                'warehouse_to_location',
                'location_to_warehouse',
                'warehouse_to_warehouse',
                'location_to_location'
            ])->after('status');
            
            $table->dateTime('estimated_arrival')->after('shipping_date');
            $table->integer('estimated_duration_minutes')->nullable()->after('estimated_arrival');
            
            // Polymorphic columns
            $table->unsignedBigInteger('origin_id')->after('trailer_id');
            $table->string('origin_type')->after('origin_id');
            
            $table->unsignedBigInteger('destination_id')->after('origin_type');
            $table->string('destination_type')->after('destination_id');
            
        });

        // Copy data from old columns to new columns
        DB::table('delivery')->update([
            'origin_id' => DB::raw('origin'),
            'origin_type' => 'App\\Models\\Location',
            'destination_id' => DB::raw('destination'),
            'destination_type' => 'App\\Models\\Location'
        ]);

        // Drop old columns after data migration
        Schema::table('delivery', function (Blueprint $table) {
            $table->dropColumn('origin');
            $table->dropColumn('destination');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add old columns
        Schema::table('delivery', function (Blueprint $table) {
            $table->unsignedBigInteger('origin')->after('trailer_id');
            $table->unsignedBigInteger('destination')->after('origin');
        });

        // Copy data back from polymorphic columns
        DB::table('delivery')
            ->where('origin_type', 'App\\Models\\Location')
            ->where('destination_type', 'App\\Models\\Location')
            ->update([
                'origin' => DB::raw('origin_id'),
                'destination' => DB::raw('destination_id')
            ]);

        // Drop new columns
        Schema::table('delivery', function (Blueprint $table) {
            $table->dropColumn('origin_id');
            $table->dropColumn('origin_type');
            $table->dropColumn('destination_id');
            $table->dropColumn('destination_type');
            $table->dropColumn('type');
            $table->dropColumn('estimated_arrival');
            $table->dropColumn('estimated_duration_minutes');
            
            $table->renameColumn('shipping_date', 'date_created');
            $table->renameColumn('completed_date', 'finished_date');
            
            $table->unsignedBigInteger('trailer_id')->nullable(false)->change();
        });

        // Re-add original foreign keys
        Schema::table('delivery', function (Blueprint $table) {
            $table->foreign('origin')->references('id')->on('location');
            $table->foreign('destination')->references('id')->on('location');
            $table->foreign('created_by')->references('id')->on('employee');
        });
    }
};
