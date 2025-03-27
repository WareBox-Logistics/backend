<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Creamos la función que hará la resta
        DB::unprepared("
            CREATE OR REPLACE FUNCTION tr_storage_rack_pallet_after_delete()
            RETURNS TRIGGER AS
            \$\$
            BEGIN
                UPDATE rack
                SET used_weight = used_weight - (
                    SELECT weight FROM pallet WHERE id = OLD.pallet
                ),
                    used_volume = used_volume - (
                    SELECT volume FROM pallet WHERE id = OLD.pallet
                )
                WHERE id = OLD.rack;

                RETURN OLD;
            END;
            \$\$
            LANGUAGE plpgsql;
        ");

        // Creamos el trigger que la invoca
        DB::unprepared("
            CREATE TRIGGER tr_storage_rack_pallet_after_delete
            AFTER DELETE ON storage_rack_pallet
            FOR EACH ROW
            EXECUTE PROCEDURE tr_storage_rack_pallet_after_delete();
        ");
    }

    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS tr_storage_rack_pallet_after_delete ON storage_rack_pallet;");
        DB::unprepared("DROP FUNCTION IF EXISTS tr_storage_rack_pallet_after_delete();");
    }
};
