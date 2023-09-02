<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Session;
use Illuminate\Http\Request;

class GameController extends Controller
{

    public function abortGame() {
       Game::where('closed', false)->update(['closed' => true]);
        event(new \App\Events\GameAbort());
        return redirect()->route('admin');
    }

    public function newGame() {

        if (Game::getOpenedGame()) {
            return redirect()->route('admin')->withErrors('Non puoi avviare un nuovo gioco quando un gioco è già in corso. Annulla il gioco corrente per poterne avviare uno nuovo');
        }

        $game = new Game();
        $game->save();

        //TODO Spara evento per notificare tutti di un nuovo gioco
        return redirect()->route('admin');

    }

    public function newQuestion(Request $request) {
        $new_question = $request->get('new_question');

        if (strlen($new_question) < 5) {
            return redirect()->route('admin')->withErrors('La domanda deve essere di almeno 5 caratteri');
        }

        $currentGame = Game::getOpenedGame();
        if (!$currentGame) {
            return redirect()->route('admin')->withErrors('Non ci sono sessioni di gioco avviate');
        }

        $currentSession = Session::getCurrentSession($currentGame);
        if ($currentSession) {
            return redirect()->route('admin')->withErrors('Non puoi fare nuove domande fintanto che la domanda corrente non è chiusa');
        }

        $newSession = Session::create(
            [
                'game_id' => $currentGame->id,
                'volunteer_id' => null,
                'question' => $new_question,
                'timestamp' => time(),
                'end_timestamp' => time() + 3600, // TODO meglio metterlo in un file in config + .env
                'interrupt_timestamp' => null,
                'closed' => false
            ]
        );

        event(new \App\Events\SendQuestion($newSession));

        return redirect()->route('admin');

    }






}
