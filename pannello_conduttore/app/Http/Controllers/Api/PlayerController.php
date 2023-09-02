<?php

namespace App\Http\Controllers\Api;

use App\Events\NewPlayerSubscribed;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Player;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class PlayerController extends Controller
{
    public function subscribe(Request $request) {

        $lastOpenedGame = Game::getOpenedGame();

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

            event(new \App\Events\RefreshGame());

            return response()->json([
                'success' => true,
                'message' => 'Benvenuto nel gioco',
                'player_id' => Crypt::encrypt($player->id), //questo dato mi serve in front per fare richieste via API, cripto per rendere vagamente più difficile falsificare la propria identita
                'plain_player_id' => $player->id, //mi serve solo per filtrare i players in f/e e sapere chi sono io nella lista
                'game_id' => $lastOpenedGame->id
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

        $lastOpenedGame = Game::getOpenedGame();

        $player_id_encrypted = $request->get('player_id');

        try {
            $player_id = Crypt::decrypt($player_id_encrypted);
        } catch (\Exception $e ) {
            return response()->json([
                'success' => false,
                'message' => 'Payload non valido... stai tentando di imbrogliare?!'
            ], 403);
        }

        $player = Player::findOrFail($player_id);

        $session = Session::getCurrentSession($lastOpenedGame);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi prenotarti per questa domanda, il tempo è scaduto!'
            ], 403);
        }

        if ($session->volunteer_id) {

            $volunteer = Player::find($session->volunteer_id)->first();

            return response()->json([
                'success' => false,
                'message' => 'Non puoi prenotarti, il concorrente ' . $volunteer->name .  ' si è già prenotato un attimo prima di te!'
            ], 403);
        }

        //controllo se l'utente ha il diritto di prenotare la risposta
        if (!$session->players->contains($player)) {

            //se sì, interrompo il gioco
            $session->interrupt_timestamp = time();
            $session->volunteer_id = $player_id;
            $session->save();

            //ed invio a tutti la notifica per la mano alzata
            event(new \App\Events\RefreshGame());

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi prenotarti, hai già provato a rispondere a questa domanda!'
            ], 403);
        }

    }



    public function newAnswer(Request $request) {

        $player_id_encrypted = $request->get('player_id');

        try {
            $player_id = Crypt::decrypt($player_id_encrypted);
        } catch (\Exception $e ) {
            return response()->json([
                'success' => false,
                'message' => 'Payload non valido... stai tentando di imbrogliare?!'
            ], 403);
        }

        $player = Player::find($player_id);

        $answer =  $request->get('answer');

        $game = Game::getOpenedGame();

        if (!$game) {
            return response()->json([
                'success' => false,
                'message' => 'Gioco non trovato'
            ], 403);
        }

        $session = Session::getCurrentSession($game);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Domanda non trovata'
            ], 403);
        }

        //controllo se l'utente ha il diritto di prenotare la risposta
        if (!$session->players->contains($player)) {
            $session->players()->attach($player, ['answer' => $answer, 'timestamp' => time()]);

            //invio un evento per notificare della risposta fornita dal player
            event(new \App\Events\RefreshGame());

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Hai già risposto a questa domanda... stai tentando di imbrogliare?!'
            ], 403);
        }

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
