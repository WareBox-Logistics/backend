<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index() {
        try{
            $deliveries =  Delivery::all();

            return response()->json([
                'deliveries'=>$deliveries
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching deliveries',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $delivery =  Delivery::findOrFail($id);

            return response()->json([
                'delivery'=>$delivery
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching delivery',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'truck'=>'required',
                'trailer'=>'required',
                'company'=>'required',
                'created_by'=>'required',
                'status'=>'required',
                'date_created'=>'required',
                'route'=>'required|json',
                'origin'=>'required',
                'destination'=>'required'
                //finished_date? we initially hace this empty (null)
            ]);

            $delivery = Delivery::create($fields);

            return response()->json([
                'delivery'=>$delivery
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a delivery',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd

    //delete
}
