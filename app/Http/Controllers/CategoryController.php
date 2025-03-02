<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        try{
            $locations =  Category::all();

            return response()->json([
                'categories'=>$locations
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching Categories',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $location =  Category::findOrFail($id);

            return response()->json([
                'category'=>$location
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching category',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'name'=>'required|max:50',
                'description'=>'required|max:100',
                'company' => 'required'
            ]);

            $location = Category::create($fields);

            return response()->json([
                'category'=>$location
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a category',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
