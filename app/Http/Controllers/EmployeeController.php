<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index(){
        try{

            $employees = Employee::all();

            return response()->json([
                'employee'=>$employees
            ]);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error fetching employees',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id){
        try{

            $employee = Employee::fing($id);

            return response()->json([
                'employee'=>$employee
            ]);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error fetching user info',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
