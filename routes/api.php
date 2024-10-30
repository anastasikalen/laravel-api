<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\RouteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('routes', RouteController::class);
    Route::apiResource('routes.places', PlaceController::class);
});
Route::middleware('auth:sanctum')->get('/user/tokens', [AuthController::class, 'tokens']);
Route::get('routes/{route}/places/{place}/weather', [PlaceController::class, 'getWeather']);
Route::get('routes/{route}/places/{place}/hotel', [PlaceController::class, 'getHotel']);

Route::get('/cache/view/{key}', function ($key) {
    return Cache::get($key, 'Cache key not found');
}); // маршрут для просмотра наличия кеша для определенного ключа


 