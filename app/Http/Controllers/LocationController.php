<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\Company;

class LocationController extends Controller
{
    public function index() {
        try{
            $locations =  Location::all();

            // $locations->map(function($location){
            //     $company = Company::where('id',$location->company)->first()->name;
            //     $location->company = $company;
            // });

            //get the company
            foreach($locations as $location){
                $company = Company::where('id',$location->company)->first();
                $location->company = $company;
            }

            return response()->json([
                'locations'=>$locations
            ]);
        
        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching locations',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $location =  Location::findOrFail($id);

            //get the company
            $company = Company::where('id',$location->company)->first();
            $location->company = $company;

            return response()->json([
                $location
            ]);
        }
        catch(\Exception $e){
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
                'company' => '',
                'id_routing_net'=>'nullable|max:100',
                'source'=>'nullable|max:100',
                'target'=>'nullable|max:100'
            ]);

            $location = Location::create($fields);

            return response()->json([
                'location'=>$location
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a location',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id){
        try{
            $location = Location::findOrFail($id);

            $fields = $request->validate([
                'name'=>'required|max:50',
                'latitude'=>'required|max:100',
                'longitude'=>'required|max:100',
                'company' => '',
                'id_routing_net'=>'nullable|max:100',
                'source'=>'nullable|max:100',
                'target'=>'nullable|max:100'
            ]);

            $location->update($fields);

            return response()->json([
                'location'=>$location
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error updating a location',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function destroy($id){
        try{
            $location = Location::findOrFail($id);
            $location->delete();

            return response()->json([
                'location'=>$location
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error deleting a location',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
