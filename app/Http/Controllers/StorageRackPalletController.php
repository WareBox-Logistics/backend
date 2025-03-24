<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StorageRackPallet;

class StorageRackPalletController extends Controller
{
    public function index()
    {
        try{
            return response()->json(["data"=>StorageRackPallet::all()]);

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function find($pallet, $rack)
    {
        try{
            $val = StorageRackPallet::where('pallet', $pallet)->where('rack', $rack)->first();
            return response()->json(["data"=>$val]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500); 
        }
    }

    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
            'pallet' => 'required|exists:pallet,id',
            'rack' => 'required|exists:rack,id',
            'position' => 'required|string|max:4',
            'level' => 'required|integer',
            'stored_at' => 'nullable|date',
            'status' => 'required|string|in:Occupied,Available',
        ]);

        return response()->json(["data"=>StorageRackPallet::create($validatedData)]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
