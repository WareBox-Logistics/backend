<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dock_assignment', function (Blueprint $table) {
            $table->dropColumn('status'); // Warning: Data loss!
            
            $table->enum('status', [
                'scheduled',
                'docking',
                'loading',
                'completed',
                'cancelled'
            ])->default('scheduled')->after('duration_minutes');
            
            $table->timestamp('completed_at')->nullable()->after('status');
        });
    }
};
