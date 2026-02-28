<?php

namespace App\Providers;

use App\Models\FeeMaster;
use App\Observers\FeeMasterObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FeeMaster::observe(FeeMasterObserver::class);
    }
}
