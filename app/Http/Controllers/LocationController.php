<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index() {
        try{
            $locations =  Location::all();

            return response()->json([
                'locations'=>$locations
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching locations',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $location =  Location::findOrFail($id);

            return response()->json([
                'location'=>$location
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching location',
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
                'is_warehouse'=>'required|boolean', //delete this, its not needed
                'company' => ''
            ]);

            $location = Location::create($fields);

            return response()->json([
                'location'=>$location
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a location',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd

    //delete
}
