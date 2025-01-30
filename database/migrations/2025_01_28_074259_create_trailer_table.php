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
        Schema::create('trailer', function (Blueprint $table) {
            $table->id();
            $table->string('plates')->unique();
            $table->string('vin')->unique();
            $table->decimal('volume', 10, 2);
            $table->enum('brand', ['Great Dane', 'Utility', 'Wabash', 'Hyundai Translead', 'Stoughton', 'Vanguard', 'Manac', 'Fontaine', 'Reitnouer', 'MAC Trailer']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trailer');
    }
};
