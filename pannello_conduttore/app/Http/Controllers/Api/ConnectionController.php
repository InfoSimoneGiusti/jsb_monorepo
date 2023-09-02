<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Session;

class ConnectionController extends Controller
{
    /*
    * Questo metodo è richiamato da front e back quando si apre la pagina per lo startup dell'istanza di Vue
    */
    public function boostrapNewConnection() {

        $current_game = false;
        $current_session = false;
        $remaining_time = 0;
        $question = "";
        $player_list = [];

        $game = Game::getOpenedGame();

        if ($game) {
            $currentSession = Session::getCurrentSession($game);
            $current_game = true;
            $current_session = (bool)$currentSession;
            $remaining_time = $currentSession?$currentSession->getRemainingTimer():0;
            $question = $currentSession?$currentSession->question:"";
            $player_list = $game->getPlayersStatus();
        }

        return response()->json(compact('current_game', 'remaining_time', 'question', 'player_list', 'current_session'));

    }

}
