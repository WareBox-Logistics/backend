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
            $employee = Employee::with('warehouse')->find($id);

            if (!$employee) {
                return response()->json([
                    'error' => 'Employee not found.'
                ], 404);
            }

            return response()->json([
                'employee' => $employee
            ], 200);
        } catch(\Exception $e){
            return response()->json([
                'error' => 'Error fetching user info',
                'message' => $e->getMessage()
            ], 500);
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

    public function getOperators() {
        try {
            
            $drivers = Employee::where('role', 4)->get();
    
            return response()->json([
                'operators' => $drivers
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
    public function update(Request $request, $id)
{
    try {
        // Validamos que warehouse sea requerido y un número entero
        $validatedData = $request->validate([
            'warehouse' => 'required|integer'
        ]);

        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'error' => 'Employee not found.'
            ], 404);
        }

        $employee->warehouse = $validatedData['warehouse'];
        $employee->save();

        return response()->json([
            'message' => 'Employee warehouse updated successfully.',
            'employee' => $employee
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error' => 'Validation failed.',
            'messages' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error updating employee.',
            'message' => $e->getMessage()
        ], 500);
    }
}
public function destroy($id)
{
    try {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'error' => 'Employee not found.'
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully.'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error deleting employee.',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
