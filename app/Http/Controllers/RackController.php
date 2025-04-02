<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rack;

class RackController extends Controller
{
    public function index(Request $request) {
        try {
            $warehouseId = $request->query('warehouse'); 
    
            $query = Rack::query();
    
            if ($warehouseId) {
                $query->where('warehouse', $warehouseId); 
            }
    
            $racks = $query->get();
    
            return response()->json(['data' => $racks]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching racks',
                'message' => $e->getMessage()
            ], 500);
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
             'levels' => 'required|integer',
             'status' => 'required|string|in:Available,Full',
             'capacity_volume' => 'required|numeric|min:0.01',
             'used_volume' => 'required|numeric|lte:capacity_volume',
             'capacity_weight' => 'required|numeric|min:0.01',
             'used_weight' => 'required|numeric|lte:capacity_weight',
             'height' => 'required|numeric|min:0.01',
             'width' => 'required|numeric|min:0.01',
             'long' => 'required|numeric|min:0.01',
         ]);
 
         return response() -> json(["data"=>Rack::create($validatedData)]);

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }
}
