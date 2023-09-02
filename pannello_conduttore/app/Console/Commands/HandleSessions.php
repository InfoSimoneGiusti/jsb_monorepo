<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HandleSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $server_time = time();

        $sessions = Session::query()
            ->where('closed', false)
            ->whereNull('interrupt_timestamp')
            ->get();

        foreach ($sessions as $session) {

            event(new \App\Events\ClockTickSession($session));

            if ($session->end_timestamp <= $server_time ||
                ($session->end_resume_interrupt_timestamp !== null && $session->end_resume_interrupt_timestamp <= $server_time)
            ) {
                $session->closed = true;
                $session->save();
                event(new \App\Events\RefreshGame('Tempo scaduto, nessun vincitore... tra pochi istanti arriva una nuova domanda!'));
            }
        }

    }
}
