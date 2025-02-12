<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index() {
        try{
            $routes =  Route::all();

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
            $route =  Route::findOrFail($id);

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
                'origin'=>'required',
                'destination'=>'required',
                'company'=>'required',
                'polyline'=>'required',
                'name'=>'required|max:100',
            ]);

            $route = Route::create($fields);

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
