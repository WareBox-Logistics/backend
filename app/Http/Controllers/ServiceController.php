<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index() {
        try{
            $services =  Service::all();

            return response()->json([
                'services'=>$services
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching services',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $service =  Service::findOrFail($id);

            return response()->json([
                'service'=>$service
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching service',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'type'=>'required|max:25',
            ]);

            $service = Service::create($fields);

            return response()->json([
                'service'=>$service
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a service',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd
    public function update(Request $request, $id){
        try{
            $service = Service::findOrFail($id);

            $fields = $request->validate([
                'type'=>'required|max:25',
            ]);

            $service->update($fields);

            return response()->json([
                'service'=>$service
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error updating a service',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //delete
    public function destroy($id){
        try{
            $service = Service::findOrFail($id);

            $service->delete();

            return response()->json([
                'service'=>$service
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error deleting a service',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
