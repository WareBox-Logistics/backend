<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First remove the existing check constraint
        DB::statement('ALTER TABLE dock DROP CONSTRAINT IF EXISTS dock_type_check');
        
        // Add the column with new check constraint and default value
        DB::statement('
            ALTER TABLE dock 
            ALTER COLUMN type SET DEFAULT \'Free\',
            ADD CONSTRAINT dock_type_check CHECK (type IN (\'Free\', \'Loading\', \'Unloading\'))
        ');
        
        // Update existing null values to 'Free'
        DB::table('dock')->whereNull('type')->update(['type' => 'Free']);
    }

    public function down(): void
    {
        // Remove the check constraint
        DB::statement('ALTER TABLE dock DROP CONSTRAINT IF EXISTS dock_type_check');
        
        // Revert to original check constraint without default
        DB::statement('
            ALTER TABLE dock 
            ALTER COLUMN type DROP DEFAULT,
            ADD CONSTRAINT dock_type_check CHECK (type IN (\'Loading\', \'Unloading\'))
        ');
        
        // Convert any 'Free' values back to 'Loading' (or handle as needed)
        DB::table('dock')->where('type', 'Free')->update(['type' => 'Loading']);
    }
};
