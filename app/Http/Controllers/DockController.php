<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dock;

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
}
