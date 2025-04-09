<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Service;

class CompanyController extends Controller
{
    public function index() {
        try{
            $companies =  Company::all();

            //get the service
            foreach($companies as $company){
                $service = Service::where('id',$company->service)->first();
                $company->service = $service;
            }

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

            //get the service
            $service = Service::where('id',$company->service)->first();
            $company->service = $service;

            return response()->json(
                $company
            );
        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'error fetching company',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function getAllCompaniesWithServices() {
        try {
            $companies = Company::with('service')->get();

            return response()->json([
                'companies' => $companies
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching companies with services',
                'message' => $e->getMessage()
            ], 500);
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

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a company',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id){
        try{
            $company = Company::findOrFail($id);

            $fields = $request->validate([
                'name'=>'required|max:50',
                'rfc'=>'required|max:13|unique:company,rfc,' . $company->id,
                'email'=>'required|email',
                'phone'=>'required|max:10',
                'service'=>'required'
            ]);

            $company->update($fields);

            return response()->json([
                'company'=>$company
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error updating a company',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function destroy($id){
        try{
            $company = Company::findOrFail($id);
            $company->delete();

            return response()->json([
                'company'=>$company
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'error'=>'Error deleting a company',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
