<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_rack_and_pallet_after_insert()
            RETURNS TRIGGER AS $$
            DECLARE
                pallet_volume NUMERIC;
                pallet_weight NUMERIC;
                rack_capacity_volume NUMERIC;
                rack_capacity_weight NUMERIC;
                new_used_volume NUMERIC;
                new_used_weight NUMERIC;
                rack_warehouse_id INTEGER;
            BEGIN
                -- Obtener datos del pallet
                SELECT volume::NUMERIC, weight::NUMERIC INTO pallet_volume, pallet_weight FROM pallet WHERE id = NEW.pallet;
                
                -- Obtener capacidad del rack y warehouse_id
                SELECT capacity_volume::NUMERIC, capacity_weight::NUMERIC, warehouse INTO rack_capacity_volume, rack_capacity_weight, rack_warehouse_id FROM rack WHERE id = NEW.rack;
                
                -- Calcular nuevos valores
                new_used_volume := pallet_volume + COALESCE((SELECT used_volume FROM rack WHERE id = NEW.rack), 0);
                new_used_weight := pallet_weight + COALESCE((SELECT used_weight FROM rack WHERE id = NEW.rack), 0);
                
                -- Validar que no exceda
                IF new_used_volume > rack_capacity_volume THEN
                    RAISE EXCEPTION 'El volumen total excede la capacidad del rack';
                END IF;
                
                IF new_used_weight > rack_capacity_weight THEN
                    RAISE EXCEPTION 'El peso total excede la capacidad del rack';
                END IF;
                
                -- Actualizar rack
                UPDATE rack
                SET used_volume = new_used_volume,
                    used_weight = new_used_weight
                WHERE id = NEW.rack;
                
                -- Actualizar warehouse del pallet
                UPDATE pallet
                SET warehouse = rack_warehouse_id
                WHERE id = NEW.pallet;

                -- Actualizar status del pallet
                UPDATE pallet
                SET status = 'Stored'
                WHERE id = NEW.pallet;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER after_insert_storage_rack_pallet
            AFTER INSERT ON storage_rack_pallet
            FOR EACH ROW
            EXECUTE FUNCTION update_rack_and_pallet_after_insert();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_insert_storage_rack_pallet ON storage_rack_pallet;
            DROP FUNCTION IF EXISTS update_rack_and_pallet_after_insert();
        ");
    }
};