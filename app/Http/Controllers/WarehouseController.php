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
                'id_routing_net'=>'nullable|max:100',
                'source'=>'nullable|max:100',
                'target'=>'nullable|max:100'
            ]);

            $warehouse = Warehouse::create($fields);

            return response()->json([
                'warehouse'=>$warehouse
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a warehouse',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id){
        try{
            $warehouse = Warehouse::findOrFail($id);

            $fields = $request->validate([
                'name'=>'required|max:50',
                'latitude'=>'required|max:100',
                'longitude'=>'required|max:100',
                'id_routing_net'=>'nullable|max:100',
                'source'=>'nullable|max:100',
                'target'=>'nullable|max:100'
            ]);

            $warehouse->update($fields);

            return response()->json([
                'warehouse'=>$warehouse
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error updating a warehouse',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function destroy($id){
        try{
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->delete();

            return response()->json([
                'warehouse'=>$warehouse
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error deleting a warehouse',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
