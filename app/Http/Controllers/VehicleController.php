<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        try{
        return response()->json(Vehicle::all());
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plates' => 'required|string|unique:vehicles,plates|max:255',
            'vin' => 'required|string|unique:vehicles,vin|max:255',
            'model_id' => 'required|exists:modell,id',
            'volume' => 'nullable|numeric|min:0',
            'driver_id' => 'nullable|exists:employee,id',
            'type' => 'required|in:semi_truck,trailer',
        ]);

        $vehicle = Vehicle::create($validated);
        return response()->json($vehicle, 201);
    }

    public function show($id)
    {
        return response()->json(Vehicle::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $validated = $request->validate([
            'plates' => 'required|string|unique:vehicles,plates,' . $id . '|max:255',
            'vin' => 'required|string|unique:vehicles,vin,' . $id . '|max:255',
            'model_id' => 'required|exists:modell,id',
            'volume' => 'nullable|numeric|min:0',
            'driver_id' => 'nullable|exists:employee,id',
            'type' => 'required|in:semi_truck,trailer',
        ]);

        $vehicle->update($validated);
        return response()->json($vehicle);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return response()->json(['message' => 'Vehicle deleted successfully']);
    }
}
