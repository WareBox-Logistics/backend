<?php

namespace App\Http\Controllers;

use App\Models\BoxInventory;
use Illuminate\Http\Request;
use App\Models\Pallet;
use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Dock;
class PalletController extends Controller
{
     public function index()
     {
         try{

            $pallets = Pallet::all();

            //get the company and warehouse
            foreach($pallets as $pallet){
                $company = Company::where('id',$pallet->company)->first();
                $pallet->company = $company;

                //get the warehouse
                $warehouse = Warehouse::where('id',$pallet->warehouse)->first();
                $pallet->warehouse = $warehouse;
            }

            return response()->json(["pallets"=>$pallets]);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }
 
public function show($id)
{
    try {
        $pallet = Pallet::find($id);

        if (!$pallet) {
            return response()->json(['message' => 'Pallet not found'], 404);
        }

        // Get the company name
        $company = Company::where('id', $pallet->company)->first();
        $pallet->company = $company ? $company->name : null;

        // Get the warehouse name
        $warehouse = Warehouse::where('id', $pallet->warehouse)->first();
        $pallet->warehouse = $warehouse ? $warehouse->name : null;

        // Get every box in the pallet
        $boxes = BoxInventory::where('pallet', $pallet->id)->get();

        // Get the name of the product for each box
        foreach ($boxes as $box) {
            $product = Product::where('id', $box->product)->first();
            $box->product = $product ? $product->name : null;
        }

        $pallet->boxes = $boxes;

        // Return the response
        return response()->json($pallet);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
public function getDashboardStats()
{
    try {
        // 1) Pallets sin verificar
        $palletsSinVerificar = Pallet::where('status', 'Created')
            ->where('verified', false)
            ->count();

        // 2) Pallets listos para almacenar
        $palletsPorAlmacenar = Pallet::where('status', 'Created')
            ->where('verified', true)
            ->count();

        // 3) Pallets almacenados
        $palletsAlmacenados = Pallet::where('status', 'Stored')
            ->count();

        // 4) Docks reservados (asumiendo que 'reserved' en DockAssignment indica un dock reservado)
        $docksReservados = Dock::where('status', 'Available')->count();

        // Retornamos un JSON con los conteos
        return response()->json([
            'palletsSinVerificar' => $palletsSinVerificar,
            'palletsPorAlmacenar' => $palletsPorAlmacenar,
            'palletsAlmacenados'  => $palletsAlmacenados,
            'docksReservados'     => $docksReservados,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error getting dashboard stats',
            'error'   => $e->getMessage()
        ], 500);
    }
}

public function getPalletsByFilter(Request $request)
{
    try {
        // Leemos los query params ?status=Created&verified=false, etc.
        $status = $request->input('status');    // "Created" / "Stored" / "In Transit" ...
        $verified = $request->input('verified'); // "true" / "false"

        // Armamos una query con las relaciones que necesites
        // Por ejemplo: 'warehouse', 'company' y la relación 'boxInventories.product'
        $query = Pallet::with(['warehouse', 'company', 'boxInventories.product']);

        // Si llega "status", filtramos
        if (!is_null($status)) {
            $query->where('status', $status);
        }

        // Si llega "verified", lo convertimos a boolean
        if (!is_null($verified)) {
            // convierte "true" / "false" en bool real
            $verifiedBool = filter_var($verified, FILTER_VALIDATE_BOOLEAN);
            $query->where('verified', $verifiedBool);
        }

        // Obtenemos los pallets que cumplan con las condiciones
        $pallets = $query->get();

        return response()->json([
            'message' => 'Filtered pallets retrieved successfully',
            'pallets' => $pallets
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error fetching filtered pallets',
            'error' => $e->getMessage()
        ], 500);
    }
}
     public function store(Request $request)
     {
        try{
             $validatedData = $request->validate([
             'company' => 'required|exists:company,id',
             'warehouse' => 'required|exists:warehouse,id',
             'weight' => 'required|numeric|min:0.01',
             'volume' => 'required|numeric|min:0.01',
             'status' => 'required|string|in:Created,Stored,In Transit,Delivered',
             'verified' => 'required|boolean',
         ]);
 
         return response() -> json(Pallet::create($validatedData), 201);

        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
     }

     public function PalletsFromWarehouse(Request $request)
     {
         try {
             $validatedData = $request->validate([
                 'warehouseID' => 'required|exists:warehouse,id',
                 'companyID' => 'required|exists:company,id'
             ]);
     
             $pallets = Pallet::with(['company', 'warehouse'])
             ->where('warehouse', $validatedData['warehouseID'])
             ->where('company', $validatedData['companyID'])
             ->where(function($query) {
                $query->where('status', 'Stored');
            })
             ->get();
     
             if ($pallets->isEmpty()) {
                return response()->json([
                    'message' => 'No stored pallets found for this company in the specified warehouse'
                ], 404);
            }
     
             return response()->json(["pallets" => $pallets]);
         } catch (\Exception $e) {
             return response()->json(['message' => $e->getMessage()], 500);
         }
    }

    public function getAllPalletsWithDetails()
    {
        try {
            $pallets = Pallet::with([
                'warehouse',
                'company',
                'boxInventories.product'
            ])->get();

            return response()->json([
                'message' => 'Pallets retrieved successfully',
                'pallets' => $pallets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching pallets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPalletsByCompany(Request $request)
    {
        try {
            $companyId = $request->input('company_id');

            if (!$companyId) {
                return response()->json(['message' => 'Company ID is required'], 400);
            }

            $company = Company::find($companyId);

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            $pallets = Pallet::with([
                'warehouse',
                'boxInventories.product'
            ])
            ->where('company', $company->id)
            ->get();

            return response()->json([
                'company' => $company->name,
                'pallets' => $pallets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching pallets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
     
     public function destroy($id)
{
    try {
        $pallet = Pallet::find($id);

        if (!$pallet) {
            return response()->json(['message' => 'Pallet not found'], 404);
        }

        $pallet->delete();

        return response()->json(['message' => 'Pallet deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

}
