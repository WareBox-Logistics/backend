<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modell;
use App\Models\Brand;

class ModellController extends Controller
{
    public function index() {
        try {
            $models = Modell::all();

            // get the brand
            foreach ($models as $model) {
                $brand = Brand::where('id', $model->brand_id)->first();
                $model->brand = $brand;
            }

            return response()->json([
                'models' => $models
            ]);
        } 
        catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching models',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try {
            $model = Modell::findOrFail($id);

            // get the brand
            $brand = Brand::where('id', $model->brand_id)->first();
            $model->brand = $brand;

            return response()->json(
                $model
            );
        } 
        catch (\Exception $e) {
            return response()->json([
                'error'=>'error fetching model',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request) {
        try {
            $fields = $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'name' => 'required|string|max:255',
                'is_truck' => 'boolean',
                'is_trailer' => 'boolean',
                'year' => 'required|integer|min:1900|max:' . date('Y'),
            ]);

            $model = Modell::create($fields);

            return response()->json([
                'model' => $model
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error creating model',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id) {
        try {
            $model = Modell::findOrFail($id);

            $fields = $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'name' => 'required|string|max:255',
                'is_truck' => 'boolean',
                'is_trailer' => 'boolean',
                'year' => 'required|integer|min:1900|max:' . date('Y'),
            ]);

            $model->update($fields);

            return response()->json([
                'model' => $model
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error updating model',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id) {
        try {
            $model = Modell::findOrFail($id);
            $model->delete();

            return response()->json([
                'model' => $model
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'error deleting model',
                'message' => $e->getMessage()
            ]);
        }
    }
}