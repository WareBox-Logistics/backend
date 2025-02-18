<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BoxInventory;

class BoxInventoryController extends Controller
{
      public function index()
      {
          try{
            return response()->json(["data"=>BoxInventory::all()]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
      }
  
      public function find($id)
      {
        try{
          return response()->json(["data"=>BoxInventory::find($id)]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500); 
      }
    }
  
      public function store(Request $request)
      {
         try{ $validatedData = $request->validate([
              'qty' => 'required|integer|min:1',
              'weight' => 'required|numeric|min:0.01',
              'volume' => 'required|numeric|min:0.01',
              'pallet' => 'required|exists:pallet,id',
              'inventory' => 'required|exists:inventory,id',
          ]);
  
          return response() -> json(["data"=>BoxInventory::create($validatedData)]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
      }
    }
}
