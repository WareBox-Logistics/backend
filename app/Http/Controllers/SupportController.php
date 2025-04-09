<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Exception;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(){
        try{
            $support = Support::with(['issue','operator'])->get();

            return response()->json([
                'supports' => $support
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
            return response()->json([
                'supports' => Support::create($validatedData)
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function update(Request $request, $id){
        try{
            $support = Support::findOrFail($id);

            $fields = $request->validate([
                'description' => 'required|string',
                'issue' => 'required|exists:issue,id',
                'status' => 'required||in:WIP,DONE,WAIT',
                'operator' => 'required|exists:employee,id',
            ]);
            $support->update($fields);

            return response()->json([
                'supports' => $support
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'error' => 'Error updating support',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id){
        try{
            $support = Support::findOrFail($id);
            $support->delete();

            return response()->json([
                'supports' => $support
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'error' => 'error deleting support',
                'message' => $e->getMessage()
            ]);
        }
    }


}
