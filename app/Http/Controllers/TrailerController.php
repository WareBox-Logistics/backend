<?php

namespace App\Http\Controllers;

use App\Models\Trailer;
use Illuminate\Http\Request;

class TrailerController extends Controller
{
    public function index() {
        try{
            $trailers =  Trailer::all();

            return response()->json([
                'trailers'=>$trailers
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching trailers',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $trailer =  Trailer::findOrFail($id);

            return response()->json([
                'trailer'=>$trailer
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching trailer',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'plates'=>'required|max:7|unique:trailer',
                'vin'=>'required|max:17|unique:trailer',
                'volume'=>'required|numeric',
                'brand'=>'required|max:30',
            ]);

            $trailer = Trailer::create($fields);

            return response()->json([
                'trailer'=>$trailer
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a trailer',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd

    //delete
}
