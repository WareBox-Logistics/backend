<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{

    public function index() {
        try{
            $warehouses =  Warehouse::all();

            

            return response()->json([
                'warehouses'=>$warehouses
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching warehouses',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $warehouse =  Warehouse::findOrFail($id);

            return response()->json(
                $warehouse
            );
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching warehouses',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'name'=>'required|max:50',
                'latitude'=>'required|max:100',
                'longitude'=>'required|max:100',
                'id_routing_net'=>'required|max:100',
                'source'=>'required|max:100',
                'target'=>'required|max:100'
            ]);

            $warehouse = Warehouse::create($fields);

            return response()->json([
                'warehouse'=>$warehouse
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a warehouse',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd

    //delete
}
