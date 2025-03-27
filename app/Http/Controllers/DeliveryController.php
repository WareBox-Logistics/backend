<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use App\Models\DeliveryDetail;
use App\Models\Employee;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['truck', 'trailer', 'company', 'origin', 'destination'])
            ->orderBy('shipping_date', 'desc')
            ->paginate(15);

        return response()->json($deliveries);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:warehouse_to_location,location_to_warehouse,warehouse_to_warehouse,location_to_location',
            'truck' => 'required|exists:vehicles,id',
            'trailer' => 'nullable|exists:vehicles,id',
            'company_id' => 'required|exists:company,id',
            'origin_id' => 'required',
            'origin_type' => 'required|in:warehouse,location',
            'destination_id' => 'required',
            'destination_type' => 'required|in:warehouse,location',
            'shipping_date' => 'required|date',
            'estimated_arrival' => 'required|date|after:shipping_date',
            'route' => 'nullable|array',
            'route.PolylinePath' => 'required_with:route|array',
            'route.RouteDirections' => 'required_with:route|array',
            'delivery_details' => 'nullable|array',  // Changed from delivery_details to pallets
            'delivery_details.*' => 'exists:pallet,id', // Validate each pallet ID exists
            'created_by' => 'required|exists:employee,id'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $validated = $validator->validated();
    
        // Map origin/destination types to model classes
        $originType = $validated['origin_type'] === 'warehouse' ? Warehouse::class : Location::class;
        $destinationType = $validated['destination_type'] === 'warehouse' ? Warehouse::class : Location::class;

    
        // Create the delivery
        $delivery = Delivery::create([
            'type' => $validated['type'],
            'truck' => $validated['truck'],
            'trailer' => $validated['trailer'],
            'company' => $validated['company_id'],
            'created_by' => $validated['created_by'],
            'status' => Delivery::STATUS_PENDING,
            'origin_id' => $validated['origin_id'],
            'origin_type' => $originType,
            'destination_id' => $validated['destination_id'],
            'destination_type' => $destinationType,
            'shipping_date' => $validated['shipping_date'],
            'estimated_arrival' => $validated['estimated_arrival'],
            'route' => $validated['route'] ?? null,
        ]);
    
        // Calculate and save duration
        $delivery->calculateDuration();
        $delivery->save();
    
        // Add delivery details (pallets) if provided
        if (!empty($validated['delivery_details'])) {
            foreach ($validated['delivery_details'] as $palletId) {
                DeliveryDetail::create([
                    'delivery' => $delivery->id,
                    'pallet' => $palletId['pallet_id']
                ]);
            }
        }

        // Explicitly load relationships with correct column names
            $delivery->load([
                'truck', 
                'trailer', 
                'company', 
                'origin', 
                'destination',
                'deliveryDetails' => function($query) use ($delivery) {
                    $query->where('delivery', $delivery->id)->with('pallet');
                }
            ]);

            return response()->json([
                'message' => 'Delivery created successfully',
                'data' => $delivery
            ], 201);
    }

    public function show(Delivery $delivery)
    {
        return response()->json($delivery->load([
            'truck', 
            'trailer', 
            'company', 
            'origin', 
            'destination',
            'deliveryDetails.pallet'
        ]));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:Pending,Docking,Loading,Delivering,Emptying',
            'estimated_arrival' => 'sometimes|date',
            'completed_date' => 'nullable|date',
            'route' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated = $validator->validated();

        $delivery->update($validated);

        if (isset($validated['estimated_arrival'])) {
            $delivery->calculateDuration();
            $delivery->save();
        }

        return response()->json($delivery->load(['truck', 'trailer', 'company', 'origin', 'destination']));
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();
        return response()->json(['message' => 'Delivery deleted successfully']);
    }

    public function getByType($type)
    {
        $validTypes = array_keys(Delivery::$types);
        
        if (!in_array($type, $validTypes)) {
            return response()->json(['error' => 'Invalid delivery type'], 400);
        }

        $deliveries = Delivery::where('type', $type)
            ->with(['truck', 'trailer', 'company', 'origin', 'destination'])
            ->orderBy('shipping_date', 'desc')
            ->paginate(15);

        return response()->json($deliveries);
    }

    public function getDeliveriesBasedOnDriver(Request $request)
{
    try {
        $params = $request->validate([
            "driverID" => 'required|exists:employee,id'
        ]);

        // Get the driver (employee) with their assigned truck
        $driver = Employee::with('truck')->findOrFail($params['driverID']);

        if ($driver->role !== 3) {
            return response()->json([
                'message' => 'The specified employee is not a driver',
                'data' => []
            ], 400);
        }

        if (!$driver->truck) {
            return response()->json([
                'message' => 'This driver has no assigned vehicle',
                'data' => []
            ], 400);
        }

        $deliveries = Delivery::with([
                'truck',
                'trailer',
                'company',
                'origin',
                'destination',
                'deliveryDetails.pallet'
            ])
            ->where('status', 'Pending')
            ->where('truck', $driver->truck->id)
            ->get();

        return response()->json([
            'message' => 'Deliveries retrieved successfully',
            'data' => $deliveries
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error retrieving deliveries',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
