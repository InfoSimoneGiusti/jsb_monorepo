<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'timeout' => env('TIMEOUT', 30),
    'volunteer_timeout' => env('VOLUNTEER_TIMEOUT', 10),

];
