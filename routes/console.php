<?php

use App\Console\Commands\pingcheck;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::command('app:pingcheck')->everyTwoMinutes()->withoutOverlapping();
Schedule::command('app:snmpcheck')->everyTwoMinutes()->withoutOverlapping();
Schedule::command('app:sshcheck')->everyTwoHours()->withoutOverlapping();
Schedule::command('app:snmppoller')->everyFiveMinutes()->withoutOverlapping();