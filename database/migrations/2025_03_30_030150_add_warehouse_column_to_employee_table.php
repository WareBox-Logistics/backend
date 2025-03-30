<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            // Agregar la columna warehouse
            $table->unsignedBigInteger('warehouse')->nullable();

            // Establecer la clave foránea
            $table->foreign('warehouse')
                  ->references('id')
                  ->on('warehouse')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            // Quitar la clave foránea y la columna
            $table->dropForeign(['warehouse']);
            $table->dropColumn('warehouse');
        });
    }
};
