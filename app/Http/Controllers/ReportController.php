<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Exception;

class ReportController extends Controller
{
    public function index(){
        try{
            return response()->json([
                'data'=>Report::all()
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function show($id){
        try{
            $repo = Report::find($id);
            return response()->json([
                'data' => $repo
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'ubication' => 'required|string',
                'problem' => 'required|exists:problem,id',
                'issue' => 'required|boolean',
                'description' => 'required|string',
                'driver' => 'required|exists:employee,id'
            ]);
            return response()->json([
                'data' => Report::create($validatedData)
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }
}
