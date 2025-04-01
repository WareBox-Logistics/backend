<?php

namespace App\Http\Controllers;

use App\Models\BoxInventory;
use Illuminate\Http\Request;
use App\Models\Pallet;
use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;

class PalletController extends Controller
{
     public function index()
     {
         try{

            $pallets = Pallet::all();

            //get the company and warehouse
            foreach($pallets as $pallet){
                $company = Company::where('id',$pallet->company)->first();
                $pallet->company = $company;

                //get the warehouse
                $warehouse = Warehouse::where('id',$pallet->warehouse)->first();
                $pallet->warehouse = $warehouse;
            }

            return response()->json(["pallets"=>$pallets]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }
 
public function show($id)
{
    try {
        $pallet = Pallet::find($id);

        if (!$pallet) {
            return response()->json(['message' => 'Pallet not found'], 404);
        }

        // Get the company name
        $company = Company::where('id', $pallet->company)->first();
        $pallet->company = $company ? $company->name : null;

        // Get the warehouse name
        $warehouse = Warehouse::where('id', $pallet->warehouse)->first();
        $pallet->warehouse = $warehouse ? $warehouse->name : null;

        // Get every box in the pallet
        $boxes = BoxInventory::where('pallet', $pallet->id)->get();

        // Get the name of the product for each box
        foreach ($boxes as $box) {
            $product = Product::where('id', $box->product)->first();
            $box->product = $product ? $product->name : null;
        }

        $pallet->boxes = $boxes;

        // Return the response
        return response()->json($pallet);
    } catch (\Exception $e) {
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
             'verified' => 'required|boolean',
         ]);
 
         return response() -> json(Pallet::create($validatedData), 201);

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }

     public function PalletsFromWarehouse(Request $request)
     {
         try {
             $validatedData = $request->validate([
                 'warehouseID' => 'required|exists:warehouse,id',
                 'companyID' => 'required|exists:company,id'
             ]);
     
             $pallets = Pallet::with(['company', 'warehouse'])
             ->where('warehouse', $validatedData['warehouseID'])
             ->where('company', $validatedData['companyID'])
             ->where(function($query) {
                $query->where('status', 'Stored');
            })
             ->get();
     
             if ($pallets->isEmpty()) {
                return response()->json([
                    'message' => 'No stored pallets found for this company in the specified warehouse'
                ], 404);
            }
     
             return response()->json(["pallets" => $pallets]);
         } catch (\Exception $e) {
             return response()->json(['message' => $e->getMessage()], 500);
         }
     }
     public function destroy($id)
{
    try {
        $pallet = Pallet::find($id);

        if (!$pallet) {
            return response()->json(['message' => 'Pallet not found'], 404);
        }

        $pallet->delete();

        return response()->json(['message' => 'Pallet deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

}
