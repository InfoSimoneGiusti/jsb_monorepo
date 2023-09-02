<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Session;

class ConnectionController extends Controller
{
    /*
    * Questo metodo è richiamato da front e back quando si apre la pagina per lo startup dell'istanza di Vue
    */
    public function refreshGame() {
        event(new \App\Events\RefreshGame());
    }

}
