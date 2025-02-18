<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rack;

class RackController extends Controller
{
     public function index()
     {
         try{
            return response()->json(["data"=>Rack::all()]);

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }
 
     public function find($id)
     {
        try{
             return response()->json(["data"=>Rack::find($id)]);

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500); 
        }
     }
 
     public function store(Request $request)
     {
        try{
             $validatedData = $request->validate([
             'warehouse' => 'required|exists:warehouse,id',
             'section' => 'required|string|max:255',
             'level' => 'required|integer',
             'status' => 'required|string|in:Available,Full',
             'capacity_volume' => 'required|numeric|min:0.01',
             'used_volume' => 'required|numeric|max:capacity_volume',
         ]);
 
         return response() -> json(["data"=>Rack::create($validatedData)]);

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }
}
