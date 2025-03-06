<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Exception;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(){
        try{
            return response()->json([
                'data' => Support::all()
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function show($id){
        try{
            $sup = Support::find($id);
            return response()->json([
                'data' => $sup
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'description' => 'required|string',
                'issue' => 'required|exists:issue,id',
                'status' => 'required||in:WIP,DONE,WAIT',
                'operator' => 'required|exists:employee,id',
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }
}
