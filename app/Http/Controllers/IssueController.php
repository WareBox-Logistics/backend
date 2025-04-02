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

            $issue = Issue::with('operator')->get();
            return response()->json(
                [
                    'issues' => $issue
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
                'status' => 'required|',
                'description' => 'required|',
                'report' => 'required|',
                'operator' => 'required|',
                'support' => 'required|boolean',
            ]);

            return response()->json([
                'issues' => Issue::create($validatedData)
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function update(Request $request, $id){
        try{
            $issue = Issue::findOrFail($id);

            $fields = $request->validate([
                'status' => 'required|string|in:WIP,DONE,WAIT',
                'description' => 'required|string',
                'report' => 'required|exists:report,id',
                'operator' => 'required|exists:employee,id',
                'support' => 'required|boolean',
            ]);
            $issue->update($fields);

            return response()->json([
                'issues'=> $issue
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'error' => 'Error updating a report',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id){
        try{
            $issue = Issue::findOrFail($id);
            $issue->delete();

            return response()->json([
                'issues' => $issue
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'error' => 'Error deleting a company',
                'message' => $e->getMessage()
            ]);
        }
    }

}


