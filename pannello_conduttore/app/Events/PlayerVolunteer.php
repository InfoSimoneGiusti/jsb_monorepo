<?php

namespace App\Events;

use App\Models\Game;
use App\Models\Player;
use App\Models\Session;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerVolunteer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player_volunteer;
    public $player_list = [];
    public $question;

    /**
     * Create a new event instance.
     */
    public function __construct(Player $player_volunteer, Game $game)
    {
        $this->player_volunteer = $player_volunteer->name;
        $this->player_list = $game->getPlayersStatus();

        $currentSession = Session::getCurrentSession($game);

        $this->question = $currentSession?$currentSession->question:"";

        event(new \App\Events\ClockTickSession($currentSession));

    }

    public function broadcastOn()
    {
        return ['jsb-quiz-game'];
    }

    public function broadcastAs()
    {
        return 'players-info';
    }
}
