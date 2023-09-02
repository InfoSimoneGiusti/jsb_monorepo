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

    public $question;
    public $remaining_time;

    public function __construct(Session $session)
    {
        $this->question = $session->question;
        $this->remaining_time = $session->end_timestamp - $session->timestamp;
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
