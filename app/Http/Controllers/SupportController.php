<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function supportStatusStats()
    {
        try {
            // Definir los status posibles en el orden deseado
            $statuses = ['WIP', 'DONE', 'WAIT'];
            
            // Función para obtener estadísticas por rango de fechas
            $getStats = function ($startDate, $endDate) use ($statuses) {
                $stats = Support::select('status', DB::raw('count(*) as total'))
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
                'error' => 'Error getting support status statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
