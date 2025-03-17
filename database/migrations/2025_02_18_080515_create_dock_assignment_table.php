<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dock_assignment', function (Blueprint $table) {
            $table->unsignedBigInteger('dock');
            $table->unsignedBigInteger('delivery');
            $table->string('status', 50)->check("status IN ('Scheduled', 'In Progress', 'Completed', 'Cancelled')");
            $table->timestamp('scheduled_time')->nullable();
            $table->timestamps();

            $table->primary(['dock', 'delivery']);

            $table->foreign('dock')->references('id')->on('dock')->cascadeOnDelete();
            $table->foreign('delivery')->references('id')->on('delivery')->cascadeOnDelete();
        });

    //     DB::unprepared("
    //     CREATE OR REPLACE FUNCTION check_level_validity()
    //     RETURNS TRIGGER AS $$
    //     DECLARE
    //         max_level INTEGER;
    //     BEGIN
    //         SELECT levels INTO max_level FROM rack WHERE id = NEW.rack_id;
    //         IF NEW.level > max_level THEN
    //             RAISE EXCEPTION 'Nivel % es mayor que el máximo permitido % para el rack %', NEW.level, max_level, NEW.rack_id;
    //         END IF;
    //         RETURN NEW;
    //     END;
    //     $$ LANGUAGE plpgsql;

    //     CREATE TRIGGER validate_level_before_insert
    //     BEFORE INSERT OR UPDATE ON storage_rack_pallets
    //     FOR EACH ROW
    //     EXECUTE FUNCTION check_level_validity();
    // ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dock_assignment');
    }
};
