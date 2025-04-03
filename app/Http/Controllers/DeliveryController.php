<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use App\Models\DeliveryDetail;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use App\Services\VehicleAvailabilityService;
use App\Models\Pallet;
use Illuminate\Support\Facades\DB;
use App\Models\DockAssignment;
use App\Models\Dock;
use Carbon\Carbon;


class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['truck', 'trailer', 'company', 'origin', 'destination'])
            ->orderBy('shipping_date', 'desc')
            ->paginate(200);

        return response()->json($deliveries);
    }

    public function store(Request $request, VehicleAvailabilityService $availabilityService)
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
            'delivery_details' => 'nullable|array',  
            'delivery_details.*' => 'exists:pallet,id',
            'created_by' => 'required|exists:employee,id'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $validated = $validator->validated();

        DB::beginTransaction();

        try {
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

        if (!empty($validated['delivery_details'])) {
            foreach ($validated['delivery_details'] as $palletId) {
                // Create delivery detail
                DeliveryDetail::create([
                    'delivery' => $delivery->id,
                    'pallet' => $palletId['pallet_id']
                ]);

                // Update pallet status to "In Transit"
                Pallet::where('id', $palletId['pallet_id'])
                    ->update([
                        'status' => 'In Transit'
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

            // if ($delivery->truck->driver) {
            //     $this->notifyDriver($delivery);
            // }

            DB::commit();

            return response()->json([
                'message' => 'Delivery created successfully',
                'data' => $delivery
            ], 201);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            Log::error('Delivery creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'message' => 'Failed to create delivery',
                'error' => $e->getMessage()
            ], 500);
        }
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
    
            // Get current date (start of day)
            $today = now()->startOfDay();
    
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
                // Only deliveries for today or future
                ->where('shipping_date', '>=', $today)
                ->orderBy('shipping_date', 'asc') // Order by soonest first
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

public function currentAndFutureDeliveries()
{
    $today = now()->format('Y-m-d'); 
    
    $deliveries = Delivery::with(['truck', 'trailer', 'company', 'origin', 'destination'])
        ->whereDate('shipping_date', '>=', $today) 
        ->orderBy('shipping_date', 'asc')
        ->get();
    
    return response()->json($deliveries);
}

protected function notifyDriver(Delivery $delivery)
{
    $driver = $delivery->truck->driver;
    
    \Illuminate\Support\Facades\Log::debug("Notification initiated for driver", [
        'driver_id' => $driver->id,
        'fcm_token_exists' => !empty($driver->fcm_token)
    ]);

    if (!$driver->fcm_token) {
        \Illuminate\Support\Facades\Log::warning("No FCM token for driver", ['driver_id' => $driver->id]);
        return;
    }

    try {
        $factory = (new \Kreait\Firebase\Factory)
            ->withServiceAccount(storage_path('app/firebase-service-account.json'));

        $messaging = $factory->createMessaging();

        $notification = \Kreait\Firebase\Messaging\Notification::create(
            'New Delivery Assigned',
            "Delivery #{$delivery->id} - {$delivery->origin->name} to {$delivery->destination->name}"
        );

        $message = \Kreait\Firebase\Messaging\CloudMessage::new()
            ->withNotification($notification)
            ->withData([
                'delivery_id' => (string)$delivery->id,
                'type' => 'new_delivery'
            ])
            ->withTarget('token', $driver->fcm_token);

        \Illuminate\Support\Facades\Log::debug("Attempting to send FCM message", [
            'token' => $driver->fcm_token,
            'message' => $message->jsonSerialize()
        ]);

        $response = $messaging->send($message);
        
        \Illuminate\Support\Facades\Log::info("FCM message sent successfully", [
            'driver_id' => $driver->id,
            'message_id' => $response instanceof \Kreait\Firebase\Messaging\SendReport ? $response->name() : 'unknown'
        ]);

    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error("FCM Error", [
            'driver_id' => $driver->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        if (str_contains($e->getMessage(), '401') || str_contains($e->getMessage(), '404')) {
            $driver->update(['fcm_token' => null]);
            \Illuminate\Support\Facades\Log::warning("Cleared invalid FCM token for driver", ['driver_id' => $driver->id]);
        }
    }
}

public function filteredDelivery($id)
{
    // Get the full delivery data with all relationships
    $delivery = Delivery::with([
        'origin:id,name',
        'destination:id,name',
        'deliveryDetails.pallet.boxInventories.product:id,name,sku'
    ])->findOrFail($id);

    // Convert the delivery to an array
    $deliveryArray = $delivery->toArray();

    // Build the response manually
    $response = [
        'delivery_id' => $deliveryArray['id'],
        'origin' => $deliveryArray['origin']['name'],
        'destination' => $deliveryArray['destination']['name'],
        'status' => $deliveryArray['status'],
        'pallets' => []
    ];

    // Process each delivery detail
    foreach ($deliveryArray['delivery_details'] as $detail) {
        // Skip if pallet data is missing
        if (!isset($detail['pallet'])) {
            continue;
        }

        $pallet = [
            'pallet_id' => $detail['pallet']['id'],
            'boxes' => []
        ];

        // Process each box in the pallet
        foreach ($detail['pallet']['box_inventories'] as $box) {
            $pallet['boxes'][] = [
                'box_id' => $box['id'],
                'product_name' => $box['product']['name'],
                'product_sku' => $box['product']['sku'],
                'quantity' => $box['qty']
            ];
        }

        $response['pallets'][] = $pallet;
    }

    return response()->json($response);
}



public function getDockAssignment($deliveryId)
{
    try {
        $delivery = Delivery::findOrFail($deliveryId);
        
        $assignment = DockAssignment::with(['dock.warehouse'])
            ->where('truck', $delivery->truck)
            ->where('scheduled_time', '>=', now())
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'No dock assignment found'], 404);
        }

        $dockData = is_int($assignment->dock) 
            ? Dock::with('warehouse')->find($assignment->dock)
            : $assignment->dock;

        return response()->json([
            'delivery_id' => $delivery->id,
            'truck_id' => $delivery->truck,
            'dock' => [
                'id' => $dockData->id,
                'number' => $dockData->number,
                'warehouse' => optional($dockData->warehouse)->name
            ],
            'scheduled_time' => $this->formatDateTime($assignment->scheduled_time),
            'status' => $assignment->status
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error retrieving dock assignment',
            'error' => $e->getMessage()
        ], 500);
    }
}

protected function formatDateTime($dateTime)
{
    if (is_string($dateTime)) {
        try {
            return Carbon::parse($dateTime)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Log::error('Failed to parse date string', [
                'date' => $dateTime,
                'error' => $e->getMessage()
            ]);
            return $dateTime; 
        }
    }
    
    if ($dateTime instanceof \Carbon\Carbon) {
        return $dateTime->format('Y-m-d H:i:s');
    }
    
    return null;
}

    public function startDelivering(Delivery $delivery)
    {
        try {
            // Validate current status
            if ($delivery->status !== Delivery::STATUS_LOADING) {
                return response()->json([
                    'message' => 'Delivery must be in Loading status to start delivering',
                    'current_status' => $delivery->status
                ], 400);
            }

            DB::transaction(function () use ($delivery) {
                // Update delivery status
                $delivery->update([
                    'status' => Delivery::STATUS_DELIVERING,
                    'updated_at' => now()
                ]);
                
                // Free the associated dock
                if ($delivery->dockAssignment) {
                    $delivery->dockAssignment->dock()->update(['status' => 'Available']);
                    $delivery->dockAssignment()->update(['status' => 'Completed']);
                }
            });

            return response()->json([
                'message' => 'Delivery status updated to Delivering and dock released',
                'delivery_id' => $delivery->id,
                'new_status' => $delivery->status,
                'dock_status' => 'Available'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update delivery status',
                'error' => $e->getMessage()
            ], 500);
        }
    }


public function setToDocking(Delivery $delivery)
{
    try {
        // Validate delivery status
        if ($delivery->status !== 'Pending') {
            return response()->json([
                'message' => 'Delivery must be in Pending status to start docking',
                'current_status' => $delivery->status
            ], 400);
        }

        DB::transaction(function () use ($delivery) {
            // Update delivery status
            $delivery->update([
                'status' => 'Docking',
                'updated_at' => now()
            ]);
            
            // Find dock assignment by vehicle and time
            $assignment = DockAssignment::where('truck', $delivery->truck)
                ->where('scheduled_time', $delivery->shipping_date)
                ->first();
            
            if ($assignment) {
                // Update the associated dock
                $assignment->dock()->update([
                    'status' => 'Occupied',
                    'type' => 'Loading'
                ]);
                
                // Optionally update assignment status if needed
                $assignment->update(['status' => 'In Progress']);
            }
        });

        return response()->json([
            'message' => 'Delivery status updated to Docking and dock prepared',
            'delivery_id' => $delivery->id,
            'new_status' => 'Docking',
            'dock_status' => 'Occupied',
            'dock_type' => 'Loading'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to update delivery and dock status',
            'error' => $e->getMessage()
        ], 500);
    }
}


        public function getStatus(Delivery $delivery)
        {
            try {
                $status = DB::table('deliveries')
                    ->where('id', $delivery->id)
                    ->value('status');

                return response()->json([
                    'delivery_id' => $delivery->id,
                    'status' => $status
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to get delivery status',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

}
