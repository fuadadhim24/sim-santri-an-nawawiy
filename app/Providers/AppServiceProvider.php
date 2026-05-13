<?php

namespace App\Providers;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\Payment;
use App\Models\Student;
use App\Observers\DiscountObserver;
use App\Observers\FeeMasterObserver;
use App\Observers\PaymentObserver;
use App\Observers\StudentObserver;
use App\Policies\BillingPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Billing::class, BillingPolicy::class);
        
        Discount::observe(DiscountObserver::class);
        FeeMaster::observe(FeeMasterObserver::class);
        Payment::observe(PaymentObserver::class);
        Student::observe(StudentObserver::class);
    }
}
