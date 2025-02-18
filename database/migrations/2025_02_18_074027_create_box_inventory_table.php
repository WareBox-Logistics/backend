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
        Schema::create('box_inventory', function (Blueprint $table) {
            $table->id();
            $table->integer('qty')->check('qty > 0');
            $table->decimal('weight')->check('weight > 0');
            $table->decimal('volume')->check('volume > 0');
            $table->unsignedBigInteger('pallet');
            $table->unsignedBigInteger('inventory');
            $table->timestamps();

            $table->foreign('pallet')->references('id')->on('pallet')->onUpdate('NO ACTION')->onDelete('CASCADE');;
            $table->foreign('inventory')->references('id')->on('inventory')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_inventory');
    }
};
