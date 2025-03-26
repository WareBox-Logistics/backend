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
    public function update(Request $request, $pallet, $rack)
    {
        try {
            $validatedData = $request->validate([
                'position' => 'sometimes|string|max:4',
                'level'    => 'sometimes|integer',
                'status'   => 'sometimes|string|in:Occupied,Available',
                'stored_at'=> 'sometimes|date',
            ]);

            // Localizamos el registro con la clave compuesta
            $storage = StorageRackPallet::where('pallet', $pallet)
                ->where('rack', $rack)
                ->firstOrFail();

            // Actualizamos los campos que vengan en $request
            $storage->update($validatedData);

            return response()->json(['data' => $storage], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    } 
    public function destroy($pallet, $rack)
    {
        try {
            $storage = StorageRackPallet::where('pallet', $pallet)
                ->where('rack', $rack)
                ->firstOrFail();

            $storage->delete();

            return response()->json(['message' => 'Record deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
