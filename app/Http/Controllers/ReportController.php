<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

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

    public function topProblems()
    {
        try {
            // Hoy
            $today = Carbon::today();
            $topToday = Report::with('problem')
                ->select('problem', DB::raw('count(*) as report_count'))
                ->whereDate('created_at', $today)
                ->groupBy('problem')
                ->orderByDesc('report_count')
                ->limit(5)
                ->get();

            // Esta semana (desde el inicio de la semana hasta hoy)
            $startOfWeek = Carbon::now()->startOfWeek();
            $topThisWeek = Report::with('problem')
                ->select('problem', DB::raw('count(*) as report_count'))
                ->whereBetween('created_at', [$startOfWeek, Carbon::now()])
                ->groupBy('problem')
                ->orderByDesc('report_count')
                ->limit(5)
                ->get();

            // Este mes (desde el inicio del mes hasta hoy)
            $startOfMonth = Carbon::now()->startOfMonth();
            $topThisMonth = Report::with('problem')
                ->select('problem', DB::raw('count(*) as report_count'))
                ->whereBetween('created_at', [$startOfMonth, Carbon::now()])
                ->groupBy('problem')
                ->orderByDesc('report_count')
                ->limit(5)
                ->get();

            // Formatear los resultados para incluir el problema y el conteo
            $formatResults = function($results) {
                return $results->map(function($item) {
                    return [
                        'problem' => $item->problem, // Datos completos del problema
                        'report_count' => $item->report_count // Cantidad de reportes
                    ];
                });
            };

            return response()->json([
                'today' => $formatResults($topToday),
                'this_week' => $formatResults($topThisWeek),
                'this_month' => $formatResults($topThisMonth)
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error getting top problems with counts',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
