<?php

namespace App\Events;

use App\Models\Game;
use App\Models\Session;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshGame implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $command = 'refresh-game';

    public $question = "";
    public $session_id = false;
    public $game_id = false;
    public $volunteer_answer = null;
    public $volunteer_name  = null;
    public $volunteer_id = null;
    public $player_list = [];
    public $remaining_time = 0;

    public function __construct()
    {
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
                        $this->volunteer_answer = $pivotData->answer;
                    }
                }

                $this->remaining_time = $session->getRemainingTimer();

            }
        }
        $this->player_list = $game->getPlayersStatus();
    }

    public function broadcastOn()
    {
        return ['jsb-quiz-game'];
    }

    public function broadcastAs()
    {
        return 'command';

    }
}
