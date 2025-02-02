<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    
    public function index() {
       try{
            $roles = Role::all();

            return response()->json([
                'roles'=>$roles
            ]);

       }catch(\Exception $e){
            return response()->json([
                'error'=>'Failed to get roles',
                'message'=>$e->getMessage()
            ]);
       }
    }

    public function show($id){
        try{

            $role = Role::find($id);

            if(!$role){
                return response()->json([
                    'message'=>'role not found'
                ]);
            }

            return response()->json([
                'role'=>$role
            ]);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error fetching the role',
                'message'=>$e->getMessage()
            ]);
        }
    }

}
