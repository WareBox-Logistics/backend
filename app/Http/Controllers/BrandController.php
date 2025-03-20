<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index() {
        try{
            $brands =  Brand::all();

            return response()->json([
                'brands'=>$brands
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching brands',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $brand =  Brand::findOrFail($id);

            return response()->json([
                $brand
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching brand',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request) {
        try {
            
            $fields = $request->validate([
                'name' => 'required|string|unique:brands,name|max:255',
            ]);

            $brand = Brand::create($fields);

            return response()->json([
                'brand' => $brand
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error creating brand',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id) {
        try {
            $brand = Brand::findOrFail($id);

            $fields = $request->validate([
                'name' => 'required|string|unique:brands,name,' . $brand->id . '|max:255',
            ]);

            $brand->update($fields);

            return response()->json([
                'brand' => $brand
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error updating brand',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id) {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();

            return response()->json([
                'brand' => $brand
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error deleting brand',
                'message' => $e->getMessage()
            ]);
        }
    }
}
