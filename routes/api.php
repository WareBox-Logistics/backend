<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DeliveryDetailController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TrailerController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoxInventoryController;
use App\Http\Controllers\PalletController;
use App\Http\Controllers\DockAssignmentController;
use App\Http\Controllers\DockController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StorageRackPalletController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\ModellController;
use App\Http\Controllers\ParkingLotController;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

//E N D P O I N T S

//AUTH 
Route::post('/registerEmployee',[AuthController::class, 'registerEmployee']);
Route::post('/loginEmployee', [AuthController::class, 'loginEmployee']);
Route::post('/logout',[AuthController::class, 'logout'])->middleware('auth:sanctum');

//ROLE
Route::apiResource('role',RoleController::class)->middleware('auth:sanctum');
//COMPANY
Route::apiResource('company',CompanyController::class)->middleware('auth:sanctum');
//Delivery
Route::apiResource('delivery',DeliveryController::class)->middleware('auth:sanctum');
Route::post('delivery-driver', [DeliveryController::class, 'getDeliveriesBasedOnDriver'])->middleware('auth:sanctum');
//Delivery Detail
Route::apiResource('delivery-detail',DeliveryDetailController::class)->middleware('auth:sanctum');
//Employee
Route::apiResource('employee',EmployeeController::class)->middleware('auth:sanctum');
//Driver
Route::get('driver', [EmployeeController::class, 'getDrivers'])->middleware('auth:sanctum');
//Location
Route::apiResource('location',LocationController::class)->middleware('auth:sanctum');
//Service
Route::apiResource('service',ServiceController::class)->middleware('auth:sanctum');
//Trailer
Route::apiResource('trailer',TrailerController::class)->middleware('auth:sanctum');
//Truck
Route::apiResource('truck',TruckController::class)->middleware('auth:sanctum');
//warehouse
Route::apiResource('warehouse',WarehouseController::class)->middleware('auth:sanctum');
//vehicle
Route::apiResource('vehicle',VehicleController::class)->middleware('auth:sanctum');
//Brand
Route::apiResource('brand',BrandController::class)->middleware('auth:sanctum');
//Model
Route::apiResource('model',ModellController::class)->middleware('auth:sanctum');

//Derian
Route::apiResource('box-inventory', BoxInventoryController::class)->middleware('auth:sanctum');
Route::apiResource('pallet', PalletController::class)->middleware('auth:sanctum');
Route::apiResource('dock-assigmnet', DockAssignmentController::class)->middleware('auth:sanctum');
Route::apiResource('dock',DockController::class)->middleware('auth:sanctum');
Route::apiResource('rack', RackController::class)->middleware('auth:sanctum');
Route::apiResource('storage-rack-pallet', StorageRackPalletController::class)->middleware('auth:sanctum');
//Pallets
Route::post('pallet/warehouse-company', [PalletController::class, 'PalletsFromWarehouse'])->middleware('auth:sanctum');

Route::put('storage-rack-pallet/{pallet}/{rack}', [StorageRackPalletController::class, 'update']);
Route::delete('storage-rack-pallet/{pallet}/{rack}', [StorageRackPalletController::class, 'destroy']);

//Dispatch
Route::apiResource('report', ReportController::class);
Route::apiResource('issue', IssueController::class);
Route::apiResource('support', SupportController::class);

//Parking
Route::post('/lots/vehicle/location', [LotController::class, 'findVehicleParkingLocation'])->middleware('auth:sanctum');
Route::apiResource('parking-lots', ParkingLotController::class)->middleware('auth:sanctum');
Route::apiResource('lots', LotController::class)->middleware('auth:sanctum');
Route::post('/generate-parking-lot', [LotController::class, 'generateParkingLot'])->middleware('auth:sanctum');
Route::get('/get-parkinglot-with-lots', [LotController::class, 'ReturnParkingLotsWithLots'])->middleware('auth:sanctum');
Route::get('/vehicles/available-trucks', [VehicleController::class, 'availableTrucks'])->middleware('auth:sanctum');
Route::get('/vehicles/available-trailers', [VehicleController::class, 'availableTrailers'])->middleware('auth:sanctum');
Route::post('/lots/assign-vehicle', [LotController::class, 'assignVehicleToLot'])->middleware('auth:sanctum');
Route::post('/lots/free', [LotController::class, 'freeLot'])->middleware('auth:sanctum');

//Caregory
Route::apiResource('category', CategoryController::class)->middleware('auth:sanctum');
//Product
Route::apiResource('product', ProductController::class)->middleware('auth:sanctum');
Route::get('product/company/{company}', [ProductController::class, 'getAllProductsByCompany'])->middleware('auth:sanctum');

Route::post('/proxy/optima', function (Request $request) {
    try {
        $queryParams = http_build_query($request->all());
        $response = Http::post("https://gaia.inegi.org.mx/sakbe_v3.1/optima?$queryParams");

        // Return the response from the external API
        return response()->json($response->json(), $response->status());
    } catch (\Exception $e) {
        // Log the error
        Log::error('Error fetching data:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Error fetching data',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::post('/proxy/optima/details', function (Request $request) {
    try {
        $queryParams = http_build_query($request->all());
        $response = Http::post("https://gaia.inegi.org.mx/sakbe_v3.1/detalle_o?$queryParams");

        // Return the response from the external API
        return response()->json($response->json(), $response->status());
    } catch (\Exception $e) {
        // Log the error
        Log::error('Error fetching data:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Error fetching data',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::post('/proxy/coordsID', function (Request $request){
    try {
        $queryParams = http_build_query($request->all());
        $response = Http::post("https://gaia.inegi.org.mx/sakbe_v3.1/buscalinea?$queryParams");

        // Return the response from the external API
        return response()->json($response->json(), $response->status());
    } catch (\Exception $e) {
        // Log the error
        Log::error('Error fetching data:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Error fetching data',
            'message' => $e->getMessage(),
        ], 500);
    }
});