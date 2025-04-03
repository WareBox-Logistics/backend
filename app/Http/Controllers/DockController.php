<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dock;
use App\Models\DockAssignment;
use Illuminate\Support\Carbon;
use App\Models\Delivery;

class DockController extends Controller
{
    public function index()
    {
        try{
            return response()-> json(Dock::all());
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function find($id)
    {
        try{
        return response() -> json(Dock::find($id));
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500); 
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|string|in:Available,Occupied,Maintenance',
                'warehouse' => 'required|exists:warehouse,id',
                'number' => 'required|integer|min:1' 
            ]);
    
            $warehouseId = $validatedData['warehouse'];
            $numberOfPorts = $validatedData['number'];
    
            $createdPorts = [];
    
            for ($i = 1; $i <= $numberOfPorts; $i++) {
                $portData = [
                    'status' => $validatedData['status'],
                    'warehouse' => $warehouseId,
                    'number' => $i 
                ];
    
                $createdPorts[] = Dock::create($portData);
            }
    
            return response()->json( $createdPorts, 201);
    
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getByWarehouse($warehouseId)
{
    try {
        if (!is_numeric($warehouseId)) {
            return response()->json(['message' => 'Invalid warehouse ID'], 400);
        }

        $docks = Dock::with('warehouse')
                    ->where('warehouse', $warehouseId)
                    ->orderBy('number', 'asc')
                    ->get();

        if ($docks->isEmpty()) {
            return response()->json(['message' => 'No docks found for this warehouse'], 404);
        }

        return response()->json($docks);

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

public function checkAvailability(Request $request)
{
    $validated = $request->validate([
        'dock_id' => 'required|exists:dock,id',
        'start_time' => 'required|date', // Accepts both ISO and Y-m-d H:i:s formats
        'duration_minutes' => 'required|integer|min:15|max:240'
    ]);

    $start = Carbon::parse($validated['start_time']);
    $end = $start->copy()->addMinutes($validated['duration_minutes']);

    $conflictingAssignment = DockAssignment::where('dock', $validated['dock_id'])
        ->where(function($query) use ($start, $end) {
            $query->whereBetween('scheduled_time', [$start, $end])
                  ->orWhere(function($q) use ($start) {
                      $q->where('scheduled_time', '<', $start)
                        ->whereRaw(
                            '(scheduled_time + (duration_minutes * interval \'1 minute\')) > ?', 
                            [$start->format('Y-m-d H:i:s')]
                        );
                  });
        })
        ->whereIn('status', ['reserved', 'in_use'])
        ->first();

    return response()->json([
        'available' => is_null($conflictingAssignment),
        'conflict' => $conflictingAssignment ? [
            'id' => $conflictingAssignment->id,
            'scheduled_time' => $conflictingAssignment->scheduled_time->format('Y-m-d H:i:s'),
            'duration_minutes' => $conflictingAssignment->duration_minutes,
            'status' => $conflictingAssignment->status
        ] : null,
        'checked_period' => [
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s')
        ]
    ]);
}

public function reserveDock(Request $request)
{
    $validated = $request->validate([
        'dock_id' => 'required|exists:dock,id',
        'truck_id' => 'required|exists:vehicles,id',
        'start_time' => 'required|date_format:Y-m-d H:i:s',
        'duration_minutes' => 'required|integer|min:15|max:240',
        'force' => 'sometimes|boolean' // Optional force reserve
    ]);

    // First check availability
    $availabilityResponse = $this->checkAvailability(new Request([
        'dock_id' => $validated['dock_id'],
        'start_time' => $validated['start_time'],
        'duration_minutes' => $validated['duration_minutes']
    ]));

    $data = json_decode($availabilityResponse->getContent(), true);

    if (!$data['available'] && !($validated['force'] ?? false)) {
        return response()->json([
            'message' => 'Dock not available',
            'conflict' => $data['conflict']
        ], 409);
    }

    $assignment = DockAssignment::create([
        'dock' => $validated['dock_id'],
        'truck' => $validated['truck_id'],
        'scheduled_time' => $validated['start_time'],
        'duration_minutes' => $validated['duration_minutes'],
        'status' => 'reserved'
    ]);

    return response()->json([
        'message' => 'Dock reserved successfully',
        'assignment' => $assignment,
        'end_time' => Carbon::parse($assignment->scheduled_time)
                          ->addMinutes($assignment->duration_minutes)
                          ->format('Y-m-d H:i:s')
    ], 201);
}

public function releaseDock(Request $request)
{
    $validated = $request->validate([
        'assignment_id' => 'required|exists:dock_assignment,id',
        'status' => 'required|in:completed,canceled'
    ]);

    $assignment = DockAssignment::find($validated['assignment_id']);

    if ($assignment->status === 'completed') {
        return response()->json(['message' => 'Assignment already completed'], 400);
    }

    $assignment->update([
        'status' => $validated['status'],
        'completed_at' => now()
    ]);

    // Update dock status if needed
    Dock::where('id', $assignment->dock)
        ->update(['status' => 'Available']);

    return response()->json([
        'message' => 'Dock released successfully',
        'assignment' => $assignment->fresh()
    ]);
    }


    public function setToLoading(Dock $dock)
{
    try {
        if ($dock->status !== 'Occupied') { 
            return response()->json([
                'message' => 'Dock must be in Occupied status to change to Loading',
                'current_status' => $dock->status
            ], 400);
        }

        if ($dock->type !== 'Loading') {
            return response()->json([
                'message' => 'Dock is not configured for loading',
                'current_type' => $dock->type
            ], 400);
        }

        $dock->update([
            'status' => 'Loading'
        ]);

        return response()->json([
            'message' => 'Dock status updated to Loading',
            'dock_id' => $dock->id,
            'new_status' => $dock->status
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to update dock status',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
