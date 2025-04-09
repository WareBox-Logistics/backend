<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Modell;
use App\Services\VehicleAvailabilityService;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

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
                'model_id' => 'required|exists:modell,id',
                'volume' => 'nullable|numeric|min:0',
                'driver_id' => 'nullable|exists:employee,id',
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
                'model_id' => 'required|exists:modell,id',
                'volume' => 'nullable|numeric|min:0',
                'driver_id' => 'nullable|exists:employee,id',
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

    public function availableTrucks()
    {
        try {
            $trucks = Vehicle::where('type', 'semi_truck')
                             ->where('is_available', true)
                             ->get();

            foreach ($trucks as $truck) {
                $model = Modell::where('id', $truck->model_id)->first();
                $truck->model = $model;
            }

            return response()->json([
                'trucks' => $trucks,
            ]);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error fetching available trucks',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function availableTrailers()
    {
        try {
            $trailers = Vehicle::where('type', 'trailer')
                               ->where('is_available', true)
                               ->get();

            foreach ($trailers as $trailer) {
                $model = Modell::where('id', $trailer->model_id)->first();
                $trailer->model = $model;
            }

            return response()->json([
                'trailers' => $trailers,
            ]);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error fetching available trailers',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function available(Request $request, VehicleAvailabilityService $availabilityService)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'nullable|in:truck,trailer',
            'origin_id' => 'nullable|integer',
            'origin_type' => 'nullable|string|in:warehouse,location' // Simplified
        ]);
    
        $origin = null;
        if ($request->origin_id && $request->origin_type) {
            $modelClass = $request->origin_type === 'warehouse' 
                ? 'App\\Models\\Warehouse' 
                : 'App\\Models\\Location';
            $origin = $modelClass::find($request->origin_id);
        }
    
        $vehicles = $availabilityService->getAvailableVehicles(
            $request->start_date,
            $request->end_date,
            $request->type,
            $origin
        );
    
        return response()->json(['vehicles' => $vehicles]);
    }

public function reserveVehicle(Request $request, VehicleAvailabilityService $availabilityService)
{
    $request->validate([
        'vehicleID' => 'required|exists:vehicles,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'type' => 'nullable|in:delivery,other',
        'deliveryID' => 'required|exists:delivery,id'
    ]);

    $reservation = $availabilityService->reserveVehicle(
        $request->vehicleID,
        $request->start_date,
        $request->end_date,
        $request->type,
        $request->deliveryID
    );

    return response()->json( $reservation);
}

public function getDriverVehicle($driverId)
{
    // Validate driver exists
    if (!Employee::where('id', $driverId)->exists()) {
        return response()->json(['message' => 'Driver not found'], 404);
    }

    $vehicle = Vehicle::with([
            'modell.brand', // This loads the model and its related brand
            'driver',
            'activeAvailability'
        ])
        ->where('driver_id', $driverId)
               ->first();

    if (!$vehicle) {
        return response()->json(['message' => 'No vehicle assigned'], 404);
    }

    return response()->json(
        $vehicle,
    );
}

}