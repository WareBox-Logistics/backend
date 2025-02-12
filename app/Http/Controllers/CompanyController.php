<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index() {
        try{
            $companies =  Company::all();

            return response()->json([
                'companies'=>$companies
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching companies',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id) {
        try{
            $company =  Company::findOrFail($id);

            return response()->json([
                'company'=>$company
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching company',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'name'=>'required|max:50',
                'rfc'=>'required|max:13|unique:company',
                'email'=>'required|email',
                'phone'=>'required|max:10',
                'service'=>'required'
            ]);

            $company = Company::create($fields);

            return response()->json([
                'company'=>$company
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a company',
                'message'=>$e->getMessage()
            ]);
        }
    }

    //upd

    //delete
}
