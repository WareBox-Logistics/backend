<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Pallet;
use Exception;

class IssueController extends Controller
{
    public function index(){
        try{
            return response()->json(
                [
                    'data' => Pallet::all()
                ]
            );
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function show($id){
        try{
            $issu = Issue::find($id);
            return response()->json([
                'data' => $issu
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'status' => 'required|string|in:WIP,DONE,WAIT',
                'description' => 'required|string',
                'report' => 'required|exists:report,id',
                'support' => 'required|boolean',
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }


}


