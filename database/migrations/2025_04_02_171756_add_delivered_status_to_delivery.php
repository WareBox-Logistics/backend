<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::transaction(function () {
            // First create a temporary column
            DB::statement('ALTER TABLE delivery ADD COLUMN status_temp VARCHAR(255)');
            
            // Copy data to temporary column
            DB::statement('UPDATE delivery SET status_temp = status');
            
            // Drop the old enum type
            DB::statement('DROP TYPE IF EXISTS delivery_status');
            
            // Create new enum type with 'Delivered' added
            DB::statement("CREATE TYPE delivery_status AS ENUM (
                'Pending',
                'Docking',
                'Loading',
                'Delivering',
                'Emptying',
                'Delivered'
            )");
            
            // Change the column type back to our new enum
            DB::statement('ALTER TABLE delivery ALTER COLUMN status TYPE delivery_status 
                          USING status_temp::delivery_status');
            
            // Drop the temporary column
            DB::statement('ALTER TABLE delivery DROP COLUMN status_temp');
        });
    }

    public function down()
    {
        DB::transaction(function () {
            // Reverse the process if needed
            DB::statement('ALTER TABLE delivery ADD COLUMN status_temp VARCHAR(255)');
            DB::statement('UPDATE delivery SET status_temp = status::text');
            DB::statement('DROP TYPE IF EXISTS delivery_status');
            DB::statement("CREATE TYPE delivery_status AS ENUM (
                'Pending',
                'Docking',
                'Loading',
                'Delivering',
                'Emptying'
            )");
            DB::statement('ALTER TABLE delivery ALTER COLUMN status TYPE delivery_status 
                          USING status_temp::delivery_status');
            DB::statement('ALTER TABLE delivery DROP COLUMN status_temp');
        });
    }
};
