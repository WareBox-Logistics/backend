<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    DB::unprepared("
        CREATE OR REPLACE FUNCTION check_level_validity()
        RETURNS TRIGGER AS $$
        DECLARE
            max_level INTEGER;
        BEGIN
            SELECT levels INTO max_level FROM rack WHERE id = NEW.rack;
            IF NEW.level > max_level THEN
                RAISE EXCEPTION 'Nivel % es mayor que el máximo permitido % para el rack %', NEW.level, max_level, NEW.rack;
            END IF;
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;
    ");
}


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS check_level_validity();");
    }
};
