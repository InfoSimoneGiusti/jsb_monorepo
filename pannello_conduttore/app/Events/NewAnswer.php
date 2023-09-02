<?php

namespace App\Events;

use App\Models\Game;
use App\Models\Player;
use App\Models\Session;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewAnswer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player_id;
    public $answer;
    public $player_name;

    public $command = 'answered';

    /**
     * Create a new event instance.
     */
    public function __construct(Player $player, string $answer)
    {
        $this->player_id = $player->id;
        $this->player_name = $player->name;
        $this->answer = $answer;
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
