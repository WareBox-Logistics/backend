<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('dock_assignment', function (Blueprint $table) {
            $table->integer('duration_minutes')
                  ->after('scheduled_time')
                  ->default(60); // Default 1 hour
        });
    }
    
    public function down()
    {
        Schema::table('dock_assignment', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
        });
    }
};
