<?php

use Illuminate\Http\Request;
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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::post('/subscribe_current_game', [\App\Http\Controllers\Api\PlayerController::class, 'subscribe'])->name('game.subscribe');
Route::post('/volunteer', [\App\Http\Controllers\Api\PlayerController::class, 'volunteer'])->name('game.volunteer');
Route::get('/bootstrap_new_connection', [\App\Http\Controllers\Api\ConnectionController::class, 'boostrapNewConnection'])->name('system.boostrapNewConnection');
Route::post('/send_answer', [\App\Http\Controllers\Api\PlayerController::class, 'newAnswer'])->name('game.newAnswer');


