<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Pallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

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
    
    public function issueWithoutSupport(){
        try{
            $support = Issue::whereDoesntHave('supports')->get();
            // $support = Issue::all();

            return response()->json([
                'issues' => $support
            ],200);
        }catch(Exception $e){
            return response()->json([
                'error' => 'Error deleting a company',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function issueStatusStats()
    {
        try {
            // Definir los status posibles en el orden deseado
            $statuses = ['WIP', 'DONE', 'WAIT'];
            
            // Función para obtener estadísticas por rango de fechas
            $getStats = function ($startDate, $endDate) use ($statuses) {
                $stats = Issue::select('status', DB::raw('count(*) as total'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('status')
                    ->get()
                    ->pluck('total', 'status')
                    ->toArray();
                
                // Asegurar que todos los status estén presentes
                $completeStats = [];
                foreach ($statuses as $status) {
                    $completeStats[$status] = $stats[$status] ?? 0;
                }
                
                return $completeStats;
            };

            // Fechas de referencia
            $now = Carbon::now();
            $todayStart = Carbon::today();
            $weekStart = Carbon::now()->startOfWeek();
            $monthStart = Carbon::now()->startOfMonth();

            return response()->json([
                'today' => $getStats($todayStart, $now),
                'this_week' => $getStats($weekStart, $now),
                'this_month' => $getStats($monthStart, $now),
                'statuses' => $statuses // Para referencia en el frontend
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error getting issue status statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}


