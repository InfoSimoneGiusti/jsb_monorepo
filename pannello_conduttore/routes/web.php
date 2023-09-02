<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin');
});

Auth::routes([
    'register' => false, // Register Routes...
    'reset' => false, // Reset Password Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('admin');


Route::middleware('auth')->group(function () {
    Route::post('/abort_game', [\App\Http\Controllers\Api\GameController::class, 'abortGame'])->name('game.abort');
    Route::post('/new_game', [\App\Http\Controllers\Api\GameController::class, 'newGame'])->name('game.new');
    Route::post('/new_question', [\App\Http\Controllers\Api\GameController::class, 'newQuestion'])->name('question.new');
    Route::post('/mark_right', [\App\Http\Controllers\Api\GameController::class, 'markAnswerRight'])->name('answer.markright');
    Route::post('/mark_wrong', [\App\Http\Controllers\Api\GameController::class, 'markAnswerWrong'])->name('answer.markwrong');
});
