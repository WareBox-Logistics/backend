<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pallet;
use App\Models\Company;
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
 
     public function find($id)
     {
         try{

            $pallet = Pallet::find($id);

            //get the company
            $company = Company::where('id',$pallet->company)->first();
            $pallet->company = $company;

            //get the warehouse
            $warehouse = Warehouse::where('id',$pallet->warehouse)->first();
            $pallet->warehouse = $warehouse;

            return response()->json(
                $pallet
            );
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
