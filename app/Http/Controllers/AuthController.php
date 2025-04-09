<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\UserBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Models\Role;

class AuthController extends Controller
{

    public function registerEmployee(Request $request) {
        $fields = $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:employee',
            'password' => 'required|confirmed',
            'role' => 'required',
            'warehouse' => 'nullable|integer',   
        ]);

        $user = Employee::create($fields);
        $token = $user->createToken($request->email);

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ],201);
    }

    public function registerUser(Request $request) {
        $fields = $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|unique:user',
            'password' => 'required|confirmed',
            'company' => 'required',
        ]);
    
        $user = UserBox::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'company' => $fields['company'],
        ]);
    
        $token = $user->createToken('UserAuthToken')->plainTextToken;
    
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }
    

    public function loginEmployee(Request $request) {
        try {
            $request->validate([
                'email' => 'required|email|exists:employee',
                'password' => 'required'
            ]);
    
            $user = Employee::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'The provided credentials are incorrect.'
                ], 401);
            }
    
            $token = $user->createToken($user->email);
            $roleName = Role::find($user->role);
            $user->role=$roleName->name;
            $user['roleID']=$roleName->id;
    
            return response()->json([
                'user' => $user,
                'token' => $token->plainTextToken
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred during login.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function loginUser(Request $request) {
        try {
            $request->validate([
                'email' => 'required|email|exists:user,email',
                'password' => 'required',
            ]);
    
            $user = UserBox::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
    
            $token = $user->createToken('UserAuthToken')->plainTextToken;
    
            return response()->json([
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred during login.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    

        public function logout(Request $request) {
            $guard = auth()->guard();
            
            if ($guard->user() instanceof Employee) {
                $request->user('employee')->tokens()->delete();
            } elseif ($guard->user() instanceof UserBox) {
                $request->user('user')->tokens()->delete();
            }
        
            return response()->json(['message' => 'Logged out successfully'], 200);
        }
        
        public function updateFcmToken(Employee $user, Request $request)
        {
            $request->validate([
                'fcm_token' => 'required|string'
            ]);
        
            $user->update(['fcm_token' => $request->fcm_token]);
            
            return response()->json(['message' => 'FCM token updated']);
        }
}
