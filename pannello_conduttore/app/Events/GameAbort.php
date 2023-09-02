<?php

namespace App\Events;

use App\Models\Session;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameAbort implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $command = 'game-abort';

    /**
     * Create a new event instance.
     */
    public function __construct()
    {

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return ['jsb-quiz-game'];
    }

    public function broadcastAs()
    {
        return 'command';

    }
}
