<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pallet;

class PalletController extends Controller
{
     public function index()
     {
         try{
            return response()->json(["data"=>Pallet::all()]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }
 
     public function find($id)
     {
         try{
            return response()->json(["data"=>Pallet::find($id)]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500); 
        }
     }
 
     public function store(Request $request)
     {
        try{
             $validatedData = $request->validate([
             'company' => 'required|exists:company,id',
             'warehouse' => 'required|exists:warehouse,id',
             'weight' => 'required|numeric|min:0.01',
             'volume' => 'required|numeric|min:0.01',
             'status' => 'required|string|in:Created,Stored,In Transit,Delivered',
         ]);
 
         return response() -> json(["data"=>Pallet::create($validatedData)]);

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }
}
