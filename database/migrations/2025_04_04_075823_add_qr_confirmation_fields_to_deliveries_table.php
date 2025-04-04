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
        Schema::table('delivery', function (Blueprint $table) {
            $table->string('confirmation_code', 12)->nullable()->unique();
            $table->timestamp('code_generated_at')->nullable();
            $table->timestamp('code_expires_at')->nullable();
            $table->timestamp('code_used_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            //
        });
    }
};
