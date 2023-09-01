<?php

namespace App\Http\Controllers\Api;

use App\Events\NewPlayerSubscribed;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;

class PlayerController extends Controller
{
    public function subscribe(Request $request) {

        $lastOpenedGame = Game::where('closed', false)->orderBy('created_at', 'desc')->first();

        if (!$lastOpenedGame) {
            return response()->json([
                'success' => false,
                'message' => 'Nessun gioco in corso, riprova più tardi'
            ], 403);
        } else {

            //controllo per omonimie
            $name = $this->calculateName($request->get('name'), $lastOpenedGame->id);

            $player = Player::create([
                'name' => $name,
                'game_id' => $lastOpenedGame->id
            ]);

            event(new \App\Events\NewPlayerSubscribed($player, $lastOpenedGame));

            return response()->json([
                'success' => true,
                'message' => 'Benvenuto nel gioco',
                'player_id' => Crypt::encrypt($player->id) //questo dato mi serve in front per fare richieste via API, cripto per rendere vagamente più difficile falsificare la propria identita
            ]);
        }

    }


    /*
     * Metodo da chiamare per richiedere di fornire una risposta. Il metodo è molto "ingenuo" e prevede che il partecipante
     * si identifichi passando il proprio id. In un contesto reale è tassativo creare un sistema di autenticazione robusto
     * basato su token (Sanctum) o JWT (Passport).
     *
     * A seguito alla registrazione e al login, il sistema potrebbe identificarsi in modo affidabile.
     *
     */

    public function volunteer(Request $request) {




    }


    protected function calculateName($name, $game_id) {

        $unique_name = $name;

        $checkName = Player::where('name', $name)->where('game_id', $game_id)->first();
        $counter = 1;

        while($checkName) {
            $unique_name = $name . '_' . $counter;
            $counter++;
            $checkName = Player::where('name', $unique_name)->where('game_id', $game_id)->first();
        }

        return $unique_name;
    }



}
