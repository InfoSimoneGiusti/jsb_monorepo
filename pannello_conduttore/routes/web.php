<?php

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

Route::get('/send_question', function() {

    $question = 'Di che colore era il cavallo bianco di Napoleone?';

    $game = \App\Models\Game::find(1);
    if (!$game) {
        $game = new \App\Models\Game();
        $game->save();
    }

    event(new \App\Events\SendQuestion('Di che colore era il cavallo bianco di Napoleone?', $game));

});

Route::get('/', function () {
    return redirect()->route('admin');
});

Auth::routes([
    'register' => false, // Register Routes...
    'reset' => false, // Reset Password Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('admin');
