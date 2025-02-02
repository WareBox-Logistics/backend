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
        Schema::table('route', function (Blueprint $table) {
            $table->unsignedBigInteger('company')->after('destination');
            $table->foreign('company')->references('id')->on('company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route', function (Blueprint $table) {
            $table->dropForeign(['company']);
            $table->dropColumn('company');
        });
    }
};
