<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DockAssignment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;

class DockAssignmentController extends Controller
{
    public function index()
    {
        try{
            return response()->json(["data"=>DockAssignment::all()]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function find($dock, $truck)
    {
        try{
            $val = DockAssignment::where('dock', $dock)->where('truck', $truck)->first();
            return response()->json(["data"=>$val]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500); 
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'dock' => 'required|exists:dock,id',
                'truck' => 'required|exists:vehicles,id',
                'status' => 'required|string|in:Scheduled,In Progress,Completed,Cancelled',
                'scheduled_time' => 'required|date', 
            ]);
    
            $exists = DockAssignment::where('truck', $validatedData['truck'])
                        ->where('scheduled_time', $validatedData['scheduled_time'])
                        ->exists();
    
            if ($exists) {
                return response()->json([
                    'message' => 'This truck is already assigned at this time'
                ], 422);
            }
    
            $assignment = DockAssignment::create($validatedData);
            return response()->json($assignment);
    
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $truckId)
{
    try {
        $validatedData = $request->validate([
            'new_dock' => 'required|exists:dock,id',
            'status' => 'required|string|in:Scheduled,In Progress,Completed,Cancelled',
            'scheduled_time' => 'required|date',
        ]);

        $currentAssignment = DockAssignment::where('truck', $truckId)
            ->where('scheduled_time', $validatedData['scheduled_time'])
            ->first();

        if ($currentAssignment) {
            if ($currentAssignment->dock == $validatedData['new_dock']) {
                $currentAssignment->update([
                    'status' => $validatedData['status']
                ]);
                return response()->json($currentAssignment);
            }

            $currentAssignment->update([
                'dock' => $validatedData['new_dock'],
                'status' => $validatedData['status']
            ]);
            
            return response()->json($currentAssignment);
        }

        $newAssignment = DockAssignment::create([
            'truck' => $truckId,
            'dock' => $validatedData['new_dock'],
            'status' => $validatedData['status'],
            'scheduled_time' => $validatedData['scheduled_time']
        ]);

        return response()->json($newAssignment);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Update failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function assignDock(Request $request)
{
    $validated = $request->validate([
        'delivery_id' => 'required|exists:delivery,id',
        'dock_id' => 'required|exists:dock,id',
        'scheduled_time' => 'required|date',
        'duration_minutes' => 'required|integer'
    ]);

    DB::transaction(function () use ($validated) {
        // Check dock availability
        $conflictingAssignment = DockAssignment::where('dock', $validated['dock_id'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('scheduled_time', [
                    $validated['scheduled_time'],
                    Carbon::parse($validated['scheduled_time'])
                        ->addMinutes($validated['duration_minutes'])
                ]);
            })
            ->exists();

        if ($conflictingAssignment) {
            abort(409, 'Dock already booked for this time period');
        }

        DockAssignment::create([
            'delivery_id' => $validated['delivery_id'],
            'dock' => $validated['dock_id'],
            'truck' => Delivery::find($validated['delivery_id'])->truck,
            'scheduled_time' => $validated['scheduled_time'],
            'duration_minutes' => $validated['duration_minutes'],
            'status' => 'scheduled'
        ]);
    });

    return response()->json(['message' => 'Dock assigned successfully']);
}

public function dockReservations($dockId)
{
    try {
        // Validate dock exists
        if (!\App\Models\Dock::find($dockId)) {
            return response()->json(['message' => 'Dock not found'], 404);
        }

        $reservations = DockAssignment::with(['delivery', 'dock'])
            ->where('dock', $dockId)
            ->orderBy('scheduled_time')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'dock_id' => $reservation->dock,
                    'dock_number' => $reservation->dock->number ?? null,
                    'delivery_id' => $reservation->delivery_id,
                    'delivery_details' => $reservation->delivery ? [
                        'truck' => $reservation->delivery->truck,
                        'trailer' => $reservation->delivery->trailer,
                        'status' => $reservation->delivery->status
                    ] : null,
                    'scheduled_time' => $reservation->scheduled_time,
                    'duration_minutes' => $reservation->duration_minutes,
                    'status' => $reservation->status,
                    'is_past' => Carbon::parse($reservation->scheduled_time)->isPast(),
                    'is_future' => Carbon::parse($reservation->scheduled_time)->isFuture(),
                    'time_slot' => [
                        'start' => $reservation->scheduled_time,
                        'end' => Carbon::parse($reservation->scheduled_time)
                            ->addMinutes($reservation->duration_minutes)
                    ]
                ];
            });

        return response()->json([
            'data' => $reservations,
            'count' => $reservations->count(),
            'past_count' => $reservations->where('is_past', true)->count(),
            'future_count' => $reservations->where('is_future', true)->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to fetch dock reservations',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
