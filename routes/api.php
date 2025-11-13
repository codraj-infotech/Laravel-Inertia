<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;

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

// User API Routes using Froiden REST API
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', [UserApiController::class, 'index']);
    Route::post('users', [UserApiController::class, 'store']);
    Route::get('users/{id}', [UserApiController::class, 'show']);
    Route::put('users/{id}', [UserApiController::class, 'update']);
    Route::patch('users/{id}', [UserApiController::class, 'update']);
    Route::delete('users/{id}', [UserApiController::class, 'destroy']);
});
