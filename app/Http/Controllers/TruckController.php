<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    public function index() {
        try{
            $trucks =  Truck::all();

            return response()->json([
                'trucks'=>$trucks
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching trucks',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $truck =  Truck::findOrFail($id);

            return response()->json([
                'truck'=>$truck
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching truck',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'plates'=>'required|max:7|unique:truck',
                'vin'=>'required|max:17|unique:truck',
                'brand'=>'required|max:30',
                'model'=>'required|max:30',
                'driver'=>'required',
            ]);

            $truck = Truck::create($fields);

            return response()->json([
                'trailer'=>$truck
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a truck',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd

    //delete
}
