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
Route::get('/refresh', [\App\Http\Controllers\Api\ConnectionController::class, 'refreshGame'])->name('system.refresh');
Route::post('/send_answer', [\App\Http\Controllers\Api\PlayerController::class, 'newAnswer'])->name('game.newAnswer');
Route::post('/leave_game', [\App\Http\Controllers\Api\PlayerController::class, 'leaveGame'])->name('player.leaveGame');


Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/abort_game', [\App\Http\Controllers\Api\GameController::class, 'abortGame'])->name('game.abort');
    Route::post('/new_game', [\App\Http\Controllers\Api\GameController::class, 'newGame'])->name('game.new');
    Route::post('/new_question', [\App\Http\Controllers\Api\GameController::class, 'newQuestion'])->name('question.new');
    Route::post('/mark_right', [\App\Http\Controllers\Api\GameController::class, 'markAnswerRight'])->name('answer.markright');
    Route::post('/mark_wrong', [\App\Http\Controllers\Api\GameController::class, 'markAnswerWrong'])->name('answer.markwrong');
    Route::post('/disqualify', [\App\Http\Controllers\Api\GameController::class, 'disqualify'])->name('game.disqualify');
});
