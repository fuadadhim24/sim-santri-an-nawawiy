<?php

namespace App\Providers;

use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Observers\DiscountObserver;
use App\Observers\FeeMasterObserver;
use App\Observers\StudentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Discount::observe(DiscountObserver::class);
        FeeMaster::observe(FeeMasterObserver::class);
        Student::observe(StudentObserver::class);
    }
}
