<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDetail;
use Illuminate\Http\Request;

class DeliveryDetailController extends Controller
{
    public function index() {
        try{
            $details =  DeliveryDetail::all();

            return response()->json([
                'details'=>$details
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching delivery details',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $detail =  DeliveryDetail::findOrFail($id);

            return response()->json([
                'detail'=>$detail
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching delivery detail',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'delivery'=>'required',
                'product'=>'required',
                'qty' => 'required|numeric|min:1'
            ]);

            $detail = DeliveryDetail::create($fields);

            return response()->json([
                'detail'=>$detail
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a delivery detail',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd

    //delete
}
