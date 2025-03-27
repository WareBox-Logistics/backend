<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Exception;
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

    public function store(Request $request){
        try{

            $fields = $request->validate([
                'name'=>'required|max:20'
            ]);

            $role = Role::create($fields);

            return response()->json([
                'role'=>$role
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error creating a role',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id){
        try{

            $role = Role::findOrFail($id);

            $fields = $request->validate([
                'name'=>'required|max:20'
            ]);

            $role->update($fields);

            return response()->json([
                'role'=>$role
            ]);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error updating the role',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function destroy($id){
        try{

            $role = Role::findOrFail($id);

            $role->delete();

            return response()->json([
                'message'=>'Role deleted'
            ]);

        }catch(\Exception $e){
            return response()->json([
                'error'=>'Error deleting the role',
                'message'=>$e->getMessage()
            ]);
        }
    }

}
