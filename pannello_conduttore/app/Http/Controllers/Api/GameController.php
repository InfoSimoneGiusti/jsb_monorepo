<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Player;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{

    public function abortGame() {
        Game::where('closed', false)->update(['closed' => true]);
        Session::where('closed', false)->update(['closed' => true]);

        event(new \App\Events\GameAbort());

        return response()->json([
            'success' => true,
            'message' => 'Gioco annullato dal conduttore'
        ]);

    }

    public function newGame() {

        if (Game::getOpenedGame()) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi avviare un nuovo gioco quando un gioco è già in corso. Annulla il gioco corrente per poterne avviare uno nuovo'
            ], 403);
        }

        $game = new Game();
        $game->save();

        event(new \App\Events\RefreshGame());

        return response()->json([
            'success' => true,
            'message' => 'Gioco avviato'
        ]);

    }

    public function newQuestion(Request $request) {
        $new_question = $request->get('new_question');

        if (strlen($new_question) < 5) {
            return response()->json([
                'success' => false,
                'message' => 'La domanda deve essere di almeno 5 caratteri'
            ], 403);
        }

        $currentGame = Game::getOpenedGame();
        if (!$currentGame) {
            return response()->json([
                'success' => false,
                'message' => 'Non ci sono sessioni di gioco avviate'
            ], 403);
        }

        $currentSession = Session::getCurrentSession($currentGame);
        if ($currentSession) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi fare nuove domande fintanto che la domanda corrente non è chiusa'
            ], 403);
        }

        $newSession = Session::create(
            [
                'game_id' => $currentGame->id,
                'volunteer_id' => null,
                'question' => $new_question,
                'total_time' => 30, // TODO meglio metterlo in un file in config + .env
                'timestamp' => time(),
                'end_timestamp' => time() + 30, // TODO meglio metterlo in un file in config + .env
                'interrupt_timestamp' => null,
                'closed' => false
            ]
        );

        event(new \App\Events\RefreshGame('Il conduttore ha inviato una nuova domanda! Affrettati a rispondere!'));

        return response()->json([
            'success' => true,
            'message' => 'Nuova domanda inviata'
        ]);

    }


    public function markAnswerRight() {

        $game = Game::getOpenedGame();

        if ($game) {
            $session = Session::getCurrentSession($game);

            if ($session) {



                //se un player si è prenotato per rispondere
                if ($session->volunteer) {



                    if ($session && $session->players->contains($session->volunteer)) {
                        $pivotData = $session->players->find($session->volunteer)->pivot;
                        $pivotData->correct_answer = true; //assegno il punto al vincitore
                        $pivotData->save();
                        $session->closed = true; //chiudo la domanda
                        $session->save();

                        //verifico se terminare il gioco
                        $players_list = $game->getPlayersStatus();

                        $winner = false;
                        foreach ($players_list as $player) {
                           if ($player['score'] >= 5) {
                               $winner = true;
                           }
                        }

                        if ($winner) {
                            $game->closed = true;
                            $game->save();
                            event(new \App\Events\GameCompleted($session->volunteer->name));
                        } else {
                            event(new \App\Events\RefreshGame($session->volunteer->name . ' ha risposto correttamente!'));
                        }

                        return response()->json([
                            'success' => true,
                            'message' => 'Ok'
                        ]);

                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La domanda non è stata trovata'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Il gioco non è stato trovato'
            ], 403);
        }

    }


    public function markAnswerWrong() {

        $game = Game::getOpenedGame();

        if ($game) {
            $session = Session::getCurrentSession($game);

            if ($session) {



                //se un player si è prenotato per rispondere
                if ($session->volunteer) {



                    if ($session && $session->players->contains($session->volunteer)) {
                        $pivotData = $session->players->find($session->volunteer)->pivot;
                        $pivotData->correct_answer = false; //assegno il punto al vincitore
                        $pivotData->save();

                        $session->volunteer_id = null;

                        $remaining_time = $session->end_timestamp -  $session->interrupt_timestamp;

                        $session->end_timestamp = time() + $remaining_time;
                        $session->timestamp = time();
                        $session->interrupt_timestamp = null;

                        $session->resume_interrupt_timestamp = time();
                        $session->end_resume_interrupt_timestamp = time() + 10; //i giocatori potranno nuovamente prenotarsi entro 10 s

                        $session->save();

                        event(new \App\Events\RefreshGame($session->volunteer->name . ' non ha risposto correttamente, il gioco riprende!'));


                        return response()->json([
                            'success' => true,
                            'message' => 'Ko'
                        ]);

                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La domanda non è stata trovata'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Il gioco non è stato trovato'
            ], 403);
        }

    }


    public function disqualify() {

        $game = Game::getOpenedGame();

        Log::emergency('1');

        if ($game) {
            Log::emergency('2');

            $session = Session::getCurrentSession($game);

            if ($session) {
                Log::emergency('3');

                //se un player si è prenotato per rispondere
                if ($session->volunteer) {
                    Log::emergency('4');

                        Log::emergency('5');

                        //imposto la risposta come sbagliata
                        $session->players()->attach($session->volunteer, ['answer' => "", 'timestamp' => time(), "correct_answer" => false]);

                        //riprendo il gioco come se la risposta fosse errata
                        $session->volunteer_id = null;

                        $remaining_time = $session->end_timestamp -  $session->interrupt_timestamp;

                        $session->end_timestamp = time() + $remaining_time;
                        $session->timestamp = time();
                        $session->interrupt_timestamp = null;

                        $session->resume_interrupt_timestamp = time();
                        $session->end_resume_interrupt_timestamp = time() + 10; //i giocatori potranno nuovamente prenotarsi entro 10 s

                        $session->save();

                        event(new \App\Events\RefreshGame($session->volunteer->name . ' è stato squalificato dal turno, il gioco riprende!'));

                        return response()->json([
                            'success' => true,
                            'message' => 'ok'
                        ]);


                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La domanda non è stata trovata'
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Il gioco non è stato trovato'
            ], 403);
        }
    }

}
