<?php

namespace App\Http\Controllers;

use App\Models\ParkingLot;
use Illuminate\Http\Request;

class ParkingLotController extends Controller
{
    public function index()
    {
        try{
            $parkingLots = ParkingLot::all();

            if($parkingLots->empty()){
                return response()->json(
                      ["message"=>"There are no parking lots."]
                );
            }

            return response()->json(
                $parkingLots
            );

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try{
            $parkingLot = ParkingLot::findOrFail($id);

            if($parkingLot->empty()){
                return response()->json(
                      ["message"=>"This warehouse does not have a parking lot."]
                );
            }

            return response()->json(
                $parkingLot
            );

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try{
            $fields = $request->validate([
                "name"=>"required|string|max:60",
                "warehouse_id"=>"required|exists:warehouse,id",
                "rows"=>"required|integer",
                "columns"=>"required|integer"
            ]);

            $parkingLot = ParkingLot::create($fields);

            return response()->json(
                $parkingLot
            , 200);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


}
