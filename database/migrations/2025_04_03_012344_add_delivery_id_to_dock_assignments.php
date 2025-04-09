<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dock_assignment', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_id')->nullable()->after('id');
            $table->foreign('delivery_id')
                  ->references('id')
                  ->on('delivery')
                  ->cascadeOnDelete();
                  
            $table->dropColumn('truck');
        });
    }
    
    public function down()
    {
        Schema::table('dock_assignment', function (Blueprint $table) {
            $table->dropForeign(['delivery_id']);
            $table->dropColumn('delivery_id');
            $table->unsignedBigInteger('truck')->after('dock');
        });
    }
};
