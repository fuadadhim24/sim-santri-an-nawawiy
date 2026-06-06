<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    $pengumumans = \App\Models\Faq::active()->where('category', 'pengumuman')->get();
    $faqs = \App\Models\Faq::active()->where('category', '!=', 'pengumuman')->get()->groupBy('category');

    return view('welcome', compact('pengumumans', 'faqs'));
});

Route::get('/checkout-test/{package}/{price}', function($package, $price) {
    $student = \App\Models\Student::first();
    if (!$student) {
        return "Tolong tambahkan minimal 1 siswa di database untuk test Duitku.";
    }

    $billing = \App\Models\Billing::create([
        'student_id' => $student->id,
        'title' => 'Paket: ' . urldecode($package) . ' (Duitku Test)',
        'original_amount' => $price,
        'discount_applied' => 0,
        'final_amount' => $price,
        'status' => 'UNPAID',
    ]);

    return redirect()->route('duitku.pay', ['billingId' => $billing->id]);
})->name('checkout.test');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'WALI_SANTRI') {
            return redirect()->route('wali.dashboard');
        }
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:WALI_SANTRI'])->group(function () {
        Route::get('/my-dashboard', \App\Livewire\GuardianDashboard::class)->name('wali.dashboard');
        Route::get('/wali/profile', \App\Livewire\GuardianProfileEdit::class)->name('wali.profile.edit');
        Route::get('/spmb-schedules', \App\Livewire\SpmbScheduleSelection::class)->name('wali.spmb-schedules');
        Route::get('/spmb/register', \App\Livewire\SpmbStudentRegistration::class)->name('wali.spmb.register');
        Route::get('/students/{student}', \App\Livewire\StudentDetail::class)->name('wali.students.show');
        Route::get('/faq', \App\Livewire\GuardianFaqIndex::class)->name('wali.faq');
    });

    Route::middleware(['role:SUPER_ADMIN,ADMINISTRASI,BENDAHARA'])->group(function () {
        Route::get('/admin/dashboard', \App\Livewire\AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin/billings', \App\Livewire\BillingIndex::class)->name('admin.billings');
    });

    Route::middleware(['role:SUPER_ADMIN,ADMINISTRASI'])->group(function () {
        Route::get('/admin/guardians', \App\Livewire\GuardianIndex::class)->name('admin.guardians');
        Route::get('/admin/guardians/create', \App\Livewire\GuardianForm::class)->name('admin.guardians.create');
        Route::get('/admin/guardians/{guardian}/edit', \App\Livewire\GuardianForm::class)->name('admin.guardians.edit');

        Route::get('/admin/students', \App\Livewire\StudentIndex::class)->name('admin.students');
        Route::get('/admin/students/create', \App\Livewire\StudentForm::class)->name('admin.students.create');
        Route::get('/admin/students/{student}/edit', \App\Livewire\StudentForm::class)->name('admin.students.edit');
        
        Route::get('/admin/rombels', \App\Livewire\RombelManagement::class)->name('admin.rombels');
        Route::get('/admin/students/{student}', \App\Livewire\StudentDetail::class)->name('admin.students.show');
        Route::get('/admin/student-acceptance', \App\Livewire\StudentAcceptance::class)->name('admin.student-acceptance');
        Route::get('/admin/student-acceptance/{student}/confirm', \App\Livewire\StudentAcceptanceConfirm::class)->name('admin.student-acceptance-confirm');
        Route::post('/admin/students/{student}/reject', [\App\Http\Controllers\StudentAcceptanceController::class, 'reject'])->name('admin.students.reject');

        Route::get('/admin/spmb-schedules', \App\Livewire\SpmbScheduleIndex::class)->name('admin.spmb-schedules');
        Route::get('/admin/spmb-schedules/create', \App\Livewire\SpmbScheduleForm::class)->name('admin.spmb-schedules.create');
        Route::get('/admin/spmb-schedules/{id}/edit', \App\Livewire\SpmbScheduleForm::class)->name('admin.spmb-schedules.edit');

        Route::get('/admin/faqs', \App\Livewire\FaqIndex::class)->name('admin.faqs');
        Route::get('/admin/faqs/create', \App\Livewire\FaqForm::class)->name('admin.faqs.create');
        Route::get('/admin/faqs/{faq}/edit', \App\Livewire\FaqForm::class)->name('admin.faqs.edit');
    });

    Route::middleware(['role:SUPER_ADMIN,BENDAHARA'])->group(function () {
        Route::get('/admin/billings/archive', \App\Livewire\BillingArchive::class)->name('admin.billings.archive');
        Route::get('/admin/billings/create', \App\Livewire\BillingForm::class)->name('admin.billings.create');

        Route::get('/admin/fee-masters', \App\Livewire\FeeMasterIndex::class)->name('admin.fee-masters');
        Route::get('/admin/fee-masters/create', \App\Livewire\FeeMasterForm::class)->name('admin.fee-masters.create');
        Route::get('/admin/fee-masters/{feeMaster}/edit', \App\Livewire\FeeMasterForm::class)->name('admin.fee-masters.edit');
        Route::get('/admin/fee-masters/archive', \App\Livewire\FeeMasterArchive::class)->name('admin.fee-masters.archive');

        Route::get('/admin/fee-categories', \App\Livewire\FeeCategoryIndex::class)->name('admin.fee-categories');
        Route::get('/admin/fee-categories/create', \App\Livewire\FeeCategoryForm::class)->name('admin.fee-categories.create');
        Route::get('/admin/fee-categories/{feeCategory}/edit', \App\Livewire\FeeCategoryForm::class)->name('admin.fee-categories.edit');

        Route::get('/admin/discounts', \App\Livewire\DiscountIndex::class)->name('admin.discounts');
        Route::get('/admin/discounts/create', \App\Livewire\DiscountForm::class)->name('admin.discounts.create');
        Route::get('/admin/discounts/{discount}/edit', \App\Livewire\DiscountForm::class)->name('admin.discounts.edit');

        Route::get('/admin/reports/financial', \App\Livewire\FinancialReport::class)->name('admin.reports.financial');
    });

    Route::get('/receipts/{billing}', [\App\Http\Controllers\ReceiptController::class, 'show'])
        ->middleware('auth')
        ->middleware('role:SUPER_ADMIN,ADMINISTRASI,BENDAHARA,WALI_SANTRI')
        ->name('admin.receipts.show');

    Route::get('/payment/pay/{billingId}', [\App\Http\Controllers\DuitkuController::class, 'createInvoice'])->name('duitku.pay');

    Route::middleware(['role:SUPER_ADMIN'])->group(function () {
        Route::get('/admin/users', \App\Livewire\UserIndex::class)->name('admin.users');
        Route::get('/admin/users/create', \App\Livewire\UserForm::class)->name('admin.users.create');
        Route::get('/admin/users/{user}/edit', \App\Livewire\UserForm::class)->name('admin.users.edit');

    });

});

Route::post('/payment/callback', [\App\Http\Controllers\DuitkuController::class, 'callback'])->name('duitku.callback');
Route::get('/payment/return', [\App\Http\Controllers\DuitkuController::class, 'returnUrl'])->name('duitku.return');

require __DIR__.'/auth.php';
