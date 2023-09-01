<?php

namespace App\Events;

use App\Models\Game;
use App\Models\Player;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPlayerSubscribed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $new_player_name;
    public $player_list = [];

    /**
     * Create a new event instance.
     */
    public function __construct(Player $new_player, Game $game)
    {
        $this->new_player_name = $new_player->name;
        $this->player_list = $game->getPlayersWithScore();
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