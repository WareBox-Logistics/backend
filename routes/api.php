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
//Delivery Detail
Route::apiResource('delivery-detail',DeliveryDetailController::class)->middleware('auth:sanctum');
//Employee
Route::apiResource('employee',EmployeeController::class)->middleware('auth:sanctum');
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

//Derian
Route::apiResource('box-inventory', BoxInventoryController::class)->middleware('auth:sanctum');
Route::apiResource('pallet', PalletController::class)->middleware('auth:sanctum');
Route::apiResource('dock-assigmnet', DockAssignmentController::class)->middleware('auth:sanctum');
Route::apiResource('dock',DockController::class)->middleware('auth:sanctum');
Route::apiResource('rack', RackController::class)->middleware('auth:sanctum');
Route::apiResource('storage-rack-pallet', StorageRackPalletController::class)->middleware('auth:sanctum');

//Dispatch
Route::apiResource('report', ReportController::class);
Route::apiResource('issue', IssueController::class);
Route::apiResource('suppoert', SupportController::class);

//Caregory
Route::apiResource('category', CategoryController::class)->middleware('auth:sanctum');
//Product
Route::apiResource('product', ProductController::class)->middleware('auth:sanctum');











