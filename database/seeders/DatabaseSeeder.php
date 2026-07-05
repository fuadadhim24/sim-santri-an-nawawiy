<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting karena foreign key dependencies:
     * 1. SpmbSchedule — tidak ada dependency
     * 2. User/Guardian/Student — student FK ke spmb_schedule
     * 3. FeeCategory — tidak ada dependency
     * 4. FeeMaster — FK ke fee_category
     * 5. Discount — FK ke fee_master
     * 6. Billing/Payment — FK ke student, fee_master, user(admin)
     */
    public function run(): void
    {
        $this->call([
            SpmbScheduleSeeder::class,
            RombelSeeder::class,
            UserSeeder::class,
            FeeCategorySeeder::class,
            FeeMasterSeeder::class,
            DiscountSeeder::class,
            BillingSeeder::class,
            OverdueTestSeeder::class,
            FaqSeeder::class,
        ]);
    }
}
