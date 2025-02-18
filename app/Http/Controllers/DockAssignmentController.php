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
        try{
            $validatedData = $request->validate([
            'dock' => 'required|exists:dock,id',
            'truck' => 'required|exists:truck,id',
            'status' => 'required|string|in:Scheduled,In Progress,Completed,Cancelled',
            'scheduled_time' => 'nullable|date',
        ]);
        
        return response() -> json(["data"=>DockAssignment::create($validatedData)]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
