<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\SnmpMicroService;
use App\Providers\SchedulerMicroService;
use App\Providers\SNMPollerMicroService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
