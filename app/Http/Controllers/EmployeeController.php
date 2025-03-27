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

    public function getDrivers() {
        try {
            
            $drivers = Employee::where('role', 3)->get();
    
            return response()->json([
                'drivers' => $drivers
            ]);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching drivers',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getClients() {
        try {
            
            $clients = Employee::where('role', 7)->get();
    
            return response()->json([
                'clients' => $clients
            ]);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching clients',
                'message' => $e->getMessage()
            ]);
        }
    }
}
