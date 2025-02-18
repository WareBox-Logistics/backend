<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dock;

class DockController extends Controller
{
    public function index()
    {
        try{
            return response()-> json(["data"=>Dock::all()]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function find($id)
    {
        try{
        return response() -> json(["data"=>Dock::find($id)]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500); 
        }
    }

    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
            'status' => 'required|string|in:Available,Occupied,Maintenance',
            'type' => 'required|string|in:Loading,Unloading',
            'warehouse' => 'required|exists:warehouse,id',
        ]);

        return response() -> json(["data"=>Dock::create($validatedData)]);
        
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
}
}
