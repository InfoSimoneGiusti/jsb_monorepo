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

    public $id;
    public $question;
    public $server_time;
    public $end_session;

    public function __construct(string $question, Game $game)
    {

        $this->question = $question;
        $this->server_time = time();
        $this->end_session = $this->server_time + 30;

        $session = Session::create([
            'game_id' => $game->id,
            'question' => $question,
            'timestamp' => $this->server_time,
            'end_timestamp' => $this->end_session,
            'closed' => false
        ]);

        $this->id = $session->id;

    }

    public function broadcastOn()
    {
        return ['my-channel'];
    }

    public function broadcastAs()
    {
        return 'my-event';

    }
}
