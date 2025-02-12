<?php

namespace App\Http\Controllers;

use App\Models\RoutesDelivery;
use Illuminate\Http\Request;

class RoutesDeliveryController extends Controller
{
    public function index() {
        try{
            $routes =  RoutesDelivery::all();

            return response()->json([
                'routes'=>$routes
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching routes',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $route =  RoutesDelivery::findOrFail($id);

            return response()->json([
                'route'=>$route
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching route',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'delivery'=>'required',
                'route'=>'required',
                'isBackup'=>'required'
            ]);

            $route = RoutesDelivery::create($fields);

            return response()->json([
                'route'=>$route
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a route',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd

    //delete
}
