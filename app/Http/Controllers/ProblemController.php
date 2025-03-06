<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use Exception;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    public function index(){
        try{
            return response()->json([
                'data' => Problem::all()
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function find($id){
        try{
            return response()->json([
                'data' => Problem::find($id)
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'name' => 'required|string',
                'level' => 'required|integer|between:1,3',
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }
}
