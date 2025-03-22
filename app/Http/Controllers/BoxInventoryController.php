<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BoxInventory;
use App\Models\Company;
use App\Models\Pallet;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;

class BoxInventoryController extends Controller
{
      public function index()
      {
          try{

            $boxes = BoxInventory::all();

            //get the pallet and inventory
            foreach($boxes as $box){
                $pallet = Pallet::where('id',$box->pallet)->first();
                $box->pallet = $pallet;              
            }

            return response()->json(["boxes"=>$boxes]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
      }
  
      public function show($id)
      {
        try{
          
            $box = BoxInventory::find($id);

            //get the pallet
            $pallet = Pallet::where('id',$box->pallet)->first();
            $box->pallet = $pallet;

            $product = Product::where('id', $box->product)->first()->name;
            $company = Company::where('id', $pallet->company)->first()->name;
            $warehouse = Warehouse::where('id', $pallet->warehouse)->first()->name;
            $box->product = $product;
            $pallet->warehouse = $warehouse;
            $pallet->company = $company;

            return response()->json(
                $box
            );
          return response()->json(
              $box
          );
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
              'product' => 'required|exists:product,id',
              
          ]);
  
          return response() -> json(["data"=>BoxInventory::create($validatedData)]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
      }
    }
}
