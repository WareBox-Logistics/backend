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
            $table->string('position', 4);
            $table->timestamp('stored_at')->nullable();
            $table->string('status', 50)->check("status IN ('Stored', 'Removed')");
            $table->timestamps();

            $table->primary(['pallet', 'rack']);

            $table->foreign('pallet')->references('id')->on('pallet')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('rack')->references('id')->on('rack')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
