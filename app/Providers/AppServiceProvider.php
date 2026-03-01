<?php

namespace App\Providers;

use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\Payment;
use App\Models\Student;
use App\Observers\DiscountObserver;
use App\Observers\FeeMasterObserver;
use App\Observers\PaymentObserver;
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
        Payment::observe(PaymentObserver::class);
        Student::observe(StudentObserver::class);
    }
}
