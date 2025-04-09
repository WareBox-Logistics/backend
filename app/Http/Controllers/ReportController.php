<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Exception;

class ReportController extends Controller
{
    public function index(){
        try{
            $company = Report::with(['driver','problem'])->get();

            return response()->json([
                'reports'=>$company,
            ],200);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function show($id){
        try{
            $repo = Report::find($id);
            return response()->json([
                'reports' => $repo
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'latitude' => 'required|',
                'longitude' => 'required|',
                'problem' => 'required|exists:problem,id',
                'issue' => 'required|boolean',
                'description' => 'required|string',
                'driver' => 'required|exists:employee,id'
            ]);
            return response()->json([
                'reports' => Report::create($validatedData)
            ]);
        }catch(Exception $e){
            return response()->json($e);
        }
    }

    public function update(Request $request, $id){
        try{
            $report = Report::findOrFail($id);

            $fields = $request->validate([
                'latitude' => 'required|',
                'longitude' => 'required|',
                'problem' => 'required|exists:problem,id',
                'issue' => 'required|boolean',
                'description' => 'required|string',
                'driver' => 'required|exists:employee,id'
            ]);
            $report->update($fields);
            
            return response()->json([
                'reports'=> $report
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
            $report = Report::findOrFail($id);
            $report->delete();

            return response()->json([
                'reports' => $report
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'error' => 'Error deleting a company',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function reportsWithoutIssue(){
        try {
            $reports =  Report::whereDoesntHave('issues')->get();

            return response()->json([
                'reports' => $reports
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error deleting a company',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
