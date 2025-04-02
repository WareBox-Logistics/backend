<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index() {
        try {
            $products = Product::with(['company', 'category'])->get();
    
            return response()->json([
                'products' => $products
            ]);
        }
        catch(\Exception $e){
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
        try {
            $fields = $request->validate([
                'name' => 'required|max:50',
                'description' => 'required|max:100',
                'price' => 'required',
                'category' => 'required',
                'sku' => 'required',
                // 'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'image' => 'required|url',
                'company' => 'required'
            ]);

            // Manejo de la imagen
            // if ($request->hasFile('image')) {
            //     $image = $request->file('image');
            //     $imageName = time() . '_' . $fields['name'] . '_' . $fields['company'] . '.' . $image->getClientOriginalExtension();
            //     $path = $image->storeAs('product_images', $imageName, 'public');
            //     $fields['image'] = Storage::url($path);
            // }

            $product = Product::create($fields);

            return response()->json([
                'product' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error creating a product',
                'message' => $e->getMessage()
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

    public function update(Request $request, $id) {
        try {
            $product = Product::findOrFail($id);

            $fields = $request->validate([
                'name' => 'sometimes|max:50',
                'description' => 'sometimes|max:100',
                'price' => 'sometimes',
                'category' => 'sometimes',
                'sku' => 'sometimes',
                // 'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'image' => 'nullable|url',
                'company' => 'sometimes'
            ]);
    
            // Manejo de la imagen
            // if ($request->hasFile('image')) {
            //     if ($product->image && Storage::exists(str_replace('/storage', 'public', $product->image))) {
            //         Storage::delete(str_replace('/storage', 'public', $product->image));
            //     }
    
            //     $image = $request->file('image');
            //     $imageName = time() . '_' . ($fields['name'] ?? $product->name) . '_' . ($fields['company'] ?? $product->company) . '.' . $image->getClientOriginalExtension();
            //     $path = $image->storeAs('product_images', $imageName, 'public');
            //     $fields['image'] = Storage::url($path);
            // }
    
            $product->update($fields);

            return response()->json([
                'product' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error updating a product',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id){
        try{
            $product = Product::findOrFail($id);

            if ($product->image && Storage::exists(str_replace('/storage', 'public', $product->image))) {
                Storage::delete(str_replace('/storage', 'public', $product->image));
            }

            $product->delete();

            return response()->json([
                'product'=>$product
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error deleting a product',
                'message'=>$e->getMessage()
            ]);
        }
    }

}
