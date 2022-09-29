<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

foreach (config("app.routes") as $route) {
    [$method, $baseURI, $uRI] = $route;
    Route::match([$method],$uRI, [\App\Http\Controllers\GatewayController::class, 'handle']);
}

Route::post("api/auth/login", [\App\Http\Controllers\LoginController::class, 'login']);
