<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index() {
        try{
            $products =  Product::all();

            return response()->json([
                'products'=>$products
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching products',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $product =  Product::findOrFail($id);

            return response()->json([
                'product'=>$product
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching product',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'name'=>'required|max:50',
                'description'=>'required|max:100',
                'price'=>'required',
                'category' => 'required',
                'sku'=>'required',
                'image' => 'required',
                'category' => 'required',
                'company' => 'required'
            ]);

            $product = Product::create($fields);

            return response()->json([
                'product'=>$product
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a product',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
