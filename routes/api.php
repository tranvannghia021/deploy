<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\ExportController;
use App\Http\Controllers\api\WebHookController;

use Illuminate\Support\Facades\Route;


Route::get('auth/login', [LoginController::class, 'store']);
Route::post('auth/login/verify', [LoginController::class, 'loginShopify']);
Route::post('auth/refresh',[LoginController::class, 'refresh']);
Route::middleware(['authTokenApi'])->prefix('auth')->group(function(){
    Route::any('customer',[CustomerController::class,'getCustomer']);
    // logout token
    Route::post('shop-info',[LoginController::class, 'me']);
    Route::post('logout',[LoginController::class, 'logout']);
    Route::get('/export-csv',[ExportController::class,'exportCSV']);
});
// Nhận sự kiện webhook 
Route::middleware(['authHmac'])->prefix('webhook/customer')->group(function(){
    Route::any('create',[WebHookController::class,'createWebHook']);
    Route::any('update',[WebHookController::class,'updateWebHook']);
    Route::any('delete',[WebHookController::class,'deleteWebHook']);
});
