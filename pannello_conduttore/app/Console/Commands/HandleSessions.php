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
            ->where('paused', false)
            ->get();

        foreach ($sessions as $session) {

            $remaining_time = $session->end_timestamp - $server_time;

            Log::info($remaining_time . ' - ' . $session->id);

            event(new \App\Events\ClockTickSession($session, $remaining_time));
            if ($session->end_timestamp < $server_time) {
                $session->closed = true;
                $session->save();

                event(new \App\Events\TimeoutSession($session, $remaining_time));


            }
        }

    }
}
