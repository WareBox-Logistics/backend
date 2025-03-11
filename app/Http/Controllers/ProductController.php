<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index() {
        try{
            $products =  Product::all();

            //get the company and category
            foreach($products as $product){
                $company = Company::where('id',$product->company)->first();
                $product->company = $company;

                //get the category
                $category = Category::where('id',$product->category)->first();
                $product->category = $category;
            }

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

            //get the company
            $company = Company::where('id',$product->company)->first();
            $product->company = $company;

            //get category
            $category = Category::where('id',$product->category)->first();
            $product->category = $category;

            return response()->json(
                $product
            );
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

    public function findProductWithSku($sku){
        try{

            $product = Product::where('sku',$sku)->first();

            if(!$product){
                return response()->json([
                    'error'=>'Product not found'
                ]);
            }


            return response()->json([
                'product'=>$product
            ]);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error fetching product with sku',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function getAllProductsByCompany($company){
        try{
            $products = Product::where('company',$company)->get();

            if(!$products){
                return response()->json([
                    'error'=>'No products found for company'
                ]);
            }

            return response()->json(
                $products
            );

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error fetching products by company',
                'message'=>$e->getMessage()
            ]);
        }
    }

}
