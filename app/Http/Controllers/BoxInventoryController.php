<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BoxInventory;
use App\Models\Pallet;
use App\Models\Inventory;

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

                //get the inventory
                $inventory = Inventory::where('id',$box->inventory)->first();
                $box->inventory = $inventory;
            }

            return response()->json(["boxes"=>$boxes]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
      }
  
      public function find($id)
      {
        try{
          
            $box = BoxInventory::find($id);

            //get the pallet
            $pallet = Pallet::where('id',$box->pallet)->first();
            $box->pallet = $pallet;

            //get the inventory
            $inventory = Inventory::where('id',$box->inventory)->first();
            $box->inventory = $inventory;

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
              'inventory' => 'required|exists:inventory,id',
          ]);
  
          return response() -> json(["data"=>BoxInventory::create($validatedData)]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
      }
    }
}
