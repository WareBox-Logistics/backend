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
            // Drop the incorrect foreign key if it exists
            $table->dropForeign(['truck']);
            
            // Add correct foreign key to vehicles table
            $table->foreign('truck')
                  ->references('id')
                  ->on('vehicles')
                  ->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('dock_assignment', function (Blueprint $table) {
            $table->dropForeign(['truck']);
        });
    }
};
