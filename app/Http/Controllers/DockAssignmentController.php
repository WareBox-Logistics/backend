<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DockAssignment;

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
}
