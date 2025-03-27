<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Problem;
use Exception;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    public function index(){
        try{
            return response()->json([
                'problems' => Problem::all()
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
            return response()->json([
                'problems' => Problem::create($validatedData)
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function update(Request $request, $id){
        try{
            $problem = Problem::findOrFail($id);

            $fields = $request->validate([
                'name' => 'required|string',
                'level' => 'required|integer|between:1,3'
            ]);

            $problem->update($fields);

            return response()->json([
                'problems' => $problem
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'error' => 'Error updating a company',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id){
        try{
            $problem = Problem::findOrFail($id);
            $problem->delete();

            return response()->json([
                'problems' => $problem
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'error' => 'Error deleting a company',
                'message' => $e->getMessage()
            ]);
        }
    }
}
