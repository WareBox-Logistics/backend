<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        try {
            $categories = Category::all();

            //get the company
            foreach($categories as $category){
                $company = Company::where('id',$category->company)->first();
                $category->company = $company;
            }

            return response()->json([
                'categories' => $categories
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching categories',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $category =  Category::findOrFail($id);

            //get the company
            $company = Company::where('id',$category->company)->first();
            $category->company = $company;

            return response()->json(
                $category
            );
        }
        catch(\Exception $e){
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

            $category = Category::create($fields);

            return response()->json([
                'category'=>$category
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a category',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id) {
        try {
            $category = Category::findOrFail($id);

            $fields = $request->validate([
                'name' => 'required|max:50',
                'description' => 'required|max:100',
                'company' => 'required'
            ]);

            $category->update($fields);

            return response()->json([
                'category' => $category
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error updating category',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function destroy($id) {
        try {
            $category = Category::findOrFail($id);

            $category->delete();

            return response()->json([
                'message'=>'Category deleted successfully'
            ]);
        }
        catch (\Exception $e){
            return response()->json([
                'error'=>'Error deleting category',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
