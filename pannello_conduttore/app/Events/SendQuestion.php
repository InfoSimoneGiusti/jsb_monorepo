<?php

namespace App\Events;

use App\Models\Game;
use App\Models\Session;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendQuestion implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $command = 'start-session';

    public $id;
    public $question;
    public $remaining_time;

    public function __construct(string $question, Game $game)
    {

        $timer = 30; // TODO meglio metterlo in un file in config + .env

        $this->question = $question;

        $server_time = time();
        $end_session = $server_time + $timer;

        $session = Session::create([
            'game_id' => $game->id,
            'question' => $question,
            'timestamp' => $server_time,
            'end_timestamp' => $end_session,
            'interrupt_timestamp' => null,
            'closed' => false,
            'paused' => false
        ]);

        $this->remaining_time = $timer;
        $this->id = $session->id;

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
