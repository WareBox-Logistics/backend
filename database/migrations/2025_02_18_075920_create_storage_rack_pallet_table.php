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
        Schema::create('storage_rack_pallet', function (Blueprint $table) {
            $table->unsignedBigInteger('pallet');
            $table->unsignedBigInteger('rack');

            $table->char('position', 2);
            $table->unsignedInteger('level');

            $table->timestamp('stored_at')->nullable();
            $table->string('status', 50)->check("status IN ('Occupied', 'Available')");
            $table->timestamps();

            $table->foreign('pallet')->references('id')->on('pallet')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('rack')->references('id')->on('rack')->onUpdate('NO ACTION')->onDelete('CASCADE');

            $table->primary(['pallet', 'rack']);
            $table->unique(['rack','position','level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_rack_pallet');
    }
};
