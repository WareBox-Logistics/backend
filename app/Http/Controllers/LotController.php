<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\ParkingLot;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class LotController extends Controller
{
    public function index()
    {
        try{
            $Lots = Lot::all();

            if($Lots->empty()){
                return response()->json(
                      ["message"=>"There are no lots."]
                );
            }

            return response()->json(
                $Lots
            );

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try{
            $Lot = Lot::findOrFail($id);

            if($Lot->empty()){
                return response()->json(
                      ["message"=>"Such lot doesnt exist"]
                );
            }

            return response()->json(
                $Lot
            );

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try{
            $fields = $request->validate([
                "spot_code"=>"required|string|max:3",
                "parking_lot_id"=>"required|exists:parking_lots,id",
                "vehicle_id"=>"nullable|exists:vehicles,id",
                "is_occupied"=>"required|boolean",
                "allowed_type"=>"required"
            ]);

            $Lot = Lot::create($fields);

            return response()->json(
                $Lot
            , 200);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function generateParkingLot(Request $request)
    {
      try{
          $request->validate([
            'rows' => 'required|integer|min:1|max:4', 
            'columns' => 'required|integer|min:1|max:10', 
            "parking_lot_id"=>"required|exists:parking_lots,id",
            "vehicle_id"=>"nullable|exists:vehicles,id",
            "is_occupied"=>"required|boolean",
            "allowed_type"=>"required"
        ]);

        $rows = $request->input('rows');
        $columns = $request->input('columns');
        $parking_lot_id = $request->input('parking_lot_id');
        $is_occupied = $request->input('is_occupied');
        $allowed_type = $request->input('allowed_type');


        // Generate parking lot spots
        $spots = [];
        for ($row = 0; $row < $rows; $row++) {
            $rowLabel = chr(65 + $row); // Convert to A, B, C, ...
            for ($col = 1; $col <= $columns; $col++) {
                $spotCode = $rowLabel . $col; // e.g., A1, A2, B1, etc.
                $spots[] = [
                            'spot_code' => $spotCode,
                            'parking_lot_id'=> $parking_lot_id,
                            'is_occupied' => $is_occupied,
                            'allowed_type' => $allowed_type,
                            ];
            }
        }

        // Insert spots into the database
        Lot::insert($spots);

        return response()->json([
            'message' => 'Parking lot generated successfully',
            'total_spots' => count($spots),
        ], 201);
    }catch(\Exception $e){
        return response()->json([
            'message' => 'An error occurred while generating the parking lot.',
            'error' => $e->getMessage(),
        ], 500);    }
    }

    public function ReturnParkingLotsWithLots(){
        try{
            $parkingLots = ParkingLot::with('lots')->get();

            if ($parkingLots->isEmpty()) {
                return response()->json([
                    "message" => "There are no parking lots."
                ], 200);
            }

            $transformedLots = [];
            foreach ($parkingLots as $parkingLot) {
                $parkingName = $parkingLot->name;
    
                // Calculate occupied and free lots
                $occupied = $parkingLot->lots->where('is_occupied', true)->count();
                $free = $parkingLot->lots->where('is_occupied', false)->count();
    
                // Build the structure
                $transformedLots[$parkingName] = [
                    'rows' => $parkingLot->rows,
                    'columns' => $parkingLot->columns,
                    'occupied' => $occupied,
                    'free' => $free,
                    'lots' => $parkingLot->lots->map(function ($lot) {
                        $lotData = [
                            'id' => $lot->id,
                            'spot_code' => $lot->spot_code,
                            'is_occupied' => $lot->is_occupied,
                        ];
    
                        // Include vehicle information if the lot is occupied
                        if ($lot->is_occupied && $lot->vehicle_id) {
                            $vehicle = Vehicle::findOrFail($lot->vehicle_id);
                            $lotData['vehicle'] = [
                                'id' => $lot->vehicle_id,
                                'license_plate' => $vehicle->plates,
                                'type' => $vehicle->type,
                            ];
                        }
    
                        return $lotData;
                    }),
                ];
            }

            return response()->json($transformedLots, 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'An error occurred while returning all the parking lots.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function assignVehicleToLot(Request $request)
    {
        try {
            $fields = $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'lot_id' => 'required|exists:lots,id',
            ]);

            $lot = Lot::findOrFail($fields['lot_id']);

            if ($lot->is_occupied) {
                return response()->json([
                    'message' => 'The lot is already occupied.',
                ], 400); 
            }

            $vehicle = Vehicle::findOrFail($fields['vehicle_id']);

            $lot->vehicle_id = $vehicle->id;
            $lot->is_occupied = true;
            $lot->save();

            return response()->json([
                'message' => 'Vehicle assigned to lot successfully.',
                'lot' => $lot,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error assigning vehicle to lot.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function freeLot(Request $request)
    {
        try {
            $fields = $request->validate([
                'lotID' => 'required|exists:lots,id', 
            ]);
    
            $lot = Lot::findOrFail($fields['lotID']);
    
            if (!$lot->is_occupied) {
                return response()->json([
                    'message' => 'The lot is already unoccupied.',
                ], 400); // Bad request
            }
    
            $lot->vehicle_id = null;
            $lot->is_occupied = false;
            $lot->save();
    
            return response()->json([
                'message' => 'Lot freed successfully.',
                'lot' => $lot,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error freeing the lot.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
