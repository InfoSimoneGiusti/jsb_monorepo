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
        Session::where('closed', false)->update(['closed' => true]);

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
                'total_time' => 30, // TODO meglio metterlo in un file in config + .env
                'timestamp' => time(),
                'end_timestamp' => time() + 30, // TODO meglio metterlo in un file in config + .env
                'interrupt_timestamp' => null,
                'closed' => false
            ]
        );

        event(new \App\Events\RefreshGame());

        return redirect()->route('admin');

    }


    public function markAnswerRight() {

        $game = Game::getOpenedGame();

        if ($game) {
            $session = Session::getCurrentSession($game);
            $this->game_id = $game->id;
            if ($session) {
                $this->session_id = $session->id;
                $this->question = $session->question;

                //se un player si è prenotato per rispondere
                if ($session->volunteer) {
                    $this->volunteer_id = $session->volunteer->id;
                    $this->volunteer_name = $session->volunteer->name;

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

                    }
                }
            } else {
                return redirect()->route('admin')->withErrors('La domanda non è stata trovata');
            }
        } else {
            return redirect()->route('admin')->withErrors('Il gioco non è stato trovato');
        }

        return redirect()->route('admin');

    }



    public function markAnswerWrong() {

        $game = Game::getOpenedGame();

        if ($game) {
            $session = Session::getCurrentSession($game);
            $this->game_id = $game->id;
            if ($session) {
                $this->session_id = $session->id;
                $this->question = $session->question;

                //se un player si è prenotato per rispondere
                if ($session->volunteer) {
                    $this->volunteer_id = $session->volunteer->id;
                    $this->volunteer_name = $session->volunteer->name;

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

                    }
                }
            } else {
                return redirect()->route('admin')->withErrors('La domanda non è stata trovata');
            }
        } else {
            return redirect()->route('admin')->withErrors('Il gioco non è stato trovato');
        }

        return redirect()->route('admin');

    }






}
