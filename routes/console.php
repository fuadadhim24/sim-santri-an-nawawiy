<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('cache:clear')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer();
