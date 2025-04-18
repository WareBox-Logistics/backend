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
        Schema::table('dock_assignment', function (Blueprint $table) {
            $table->dropUnique(['truck']); 
            $table->unique(['truck', 'date']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dock_assignment', function (Blueprint $table) {
            $table->dropUnique(['truck', 'date']);
            $table->unique(['truck']);
        });
    }
};
