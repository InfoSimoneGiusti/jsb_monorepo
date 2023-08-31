<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;

class checkEndSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-end-sessions';

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
                    ->where('end_timestamp', '>=', $server_time)
                    ->where('closed', false)
                    ->get();

        foreach ($sessions as $session) {
            event(new \App\Events\TerminateSession($session));
            $session->closed = true;
            $session->save();
        }

    }
}
