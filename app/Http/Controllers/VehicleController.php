<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Modell;

class VehicleController extends Controller
{
    public function index() {
        try {
            $vehicles = Vehicle::all();

            // get the model
            foreach ($vehicles as $vehicle) {
                $model = Modell::where('id', $vehicle->model_id)->first();
                $vehicle->model = $model;
            }

            return response()->json([
                'vehicles' => $vehicles,
            ]);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error'=>'error fetching vehicles',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try {
            $vehicle = Vehicle::findOrFail($id);

            // get the model
            $model = Modell::where('id', $vehicle->model_id)->first();
            $vehicle->model = $model;

            return response()->json(
                $vehicle
            );
        } 
        catch (\Exception $e) {
            return response()->json([
                'error'=>'error fetching vehicle',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request) {
        try {
            $fields = $request->validate([
                'plates' => 'required|string|unique:vehicles|max:255',
                'vin' => 'required|string|unique:vehicles|max:255',
                'model_id' => 'required|exists:models,id',
                'volume' => 'nullable|numeric|min:0',
                'driver_id' => 'nullable|exists:employees,id',
                'type' => 'required|in:semi_truck,trailer',
            ]);

            $vehicle = Vehicle::create($fields);

            return response()->json([
                'vehicle' => $vehicle
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error creating vehicle',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id) {
        try {
            $vehicle = Vehicle::findOrFail($id);

            $fields = $request->validate([
                'plates' => 'required|string|unique:vehicles,plates,' . $vehicle->id . '|max:255',
                'vin' => 'required|string|unique:vehicles,vin,' . $vehicle->id . '|max:255',
                'model_id' => 'required|exists:models,id',
                'volume' => 'nullable|numeric|min:0',
                'driver_id' => 'nullable|exists:employees,id',
                'type' => 'required|in:semi_truck,trailer',
            ]);

            $vehicle->update($fields);

            return response()->json([
                'vehicle' => $vehicle
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error updating vehicle',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id) {
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();

            return response()->json([
                'vehicle' => $vehicle
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error deleting vehicle',
                'message' => $e->getMessage()
            ]);
        }
    }
}