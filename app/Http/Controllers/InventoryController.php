<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\BoxInventory;
use App\Models\Product;

class InventoryController extends Controller
{
    public function index()
    {
        try{
            $inventories = Inventory::all();

            //get the box and product
            foreach($inventories as $inventory){
                $box = BoxInventory::where('id',$inventory->box)->first();
                $inventory->box = $box;

                //get the product
                $product = Product::where('id',$inventory->product)->first();
                $inventory->product = $product;
            }

            return response()->json(["inventories"=>$inventories]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function find($id)
    {
        try{

            $inventory = Inventory::find($id);

            //get the box
            $box = BoxInventory::where('id',$inventory->box)->first();
            $inventory->box = $box;

            //get the product
            $product = Product::where('id',$inventory->product)->first();
            $inventory->product = $product;

            return response()->json(
                $inventory
            );
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500); 
        }
    }

    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'warehouse' => 'required|exists:warehouse,id',
                'product' => 'required|exists:product,id',
                'qty' => 'required|numeric',
            ]);

            $inventory = Inventory::create($validatedData);

            return response()->json([
                'message' => 'Inventory created',
                'inventory' => $inventory
            ]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
