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
        Schema::table('rack', function (Blueprint $table) {
            $table->dropColumn('height');
            $table->dropColumn('width');
            $table->dropColumn('depth');
            $table->unsignedBigInteger('levels')->check('levels > 0')->nullable();;
            $table->decimal('height')->check('height > 0')->after('levels')->nullable();;
            $table->decimal('width')->check('width > 0')->after('height')->nullable();;
            $table->decimal('long')->check('long > 0')->after('width')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     
    }
};
