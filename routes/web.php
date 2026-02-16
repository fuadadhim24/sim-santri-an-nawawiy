<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'WALI_SANTRI') {
        return redirect()->route('wali.dashboard');
    }
    return view('dashboard'); // Admin dashboard
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wali Santri Routes
    Route::middleware(['role:WALI_SANTRI'])->group(function () {
        Route::get('/my-dashboard', \App\Livewire\GuardianDashboard::class)->name('wali.dashboard');
    });

    Route::middleware(['role:SUPER_ADMIN,ADMIN_TU'])->group(function () {
        Route::get('/admin/users', \App\Livewire\UserIndex::class)->name('admin.users');
        Route::get('/admin/users/create', \App\Livewire\UserForm::class)->name('admin.users.create');
        Route::get('/admin/users/{user}/edit', \App\Livewire\UserForm::class)->name('admin.users.edit');

        Route::get('/admin/guardians', \App\Livewire\GuardianIndex::class)->name('admin.guardians');
        Route::get('/admin/guardians/create', \App\Livewire\GuardianForm::class)->name('admin.guardians.create');
        Route::get('/admin/guardians/{guardian}/edit', \App\Livewire\GuardianForm::class)->name('admin.guardians.edit');

        Route::get('/admin/students', \App\Livewire\StudentIndex::class)->name('admin.students');
        Route::get('/admin/students/create', \App\Livewire\StudentForm::class)->name('admin.students.create');
        Route::get('/admin/students/{student}/edit', \App\Livewire\StudentForm::class)->name('admin.students.edit');
        Route::get('/admin/students/{student}', \App\Livewire\StudentDetail::class)->name('admin.students.show');

        Route::get('/admin/fee-masters', \App\Livewire\FeeMasterIndex::class)->name('admin.fee-masters');
        Route::get('/admin/fee-masters/create', \App\Livewire\FeeMasterForm::class)->name('admin.fee-masters.create');
        Route::get('/admin/fee-masters/{feeMaster}/edit', \App\Livewire\FeeMasterForm::class)->name('admin.fee-masters.edit');

        Route::get('/admin/discounts', \App\Livewire\DiscountIndex::class)->name('admin.discounts');
        Route::get('/admin/discounts/create', \App\Livewire\DiscountForm::class)->name('admin.discounts.create');
        Route::get('/admin/discounts/{discount}/edit', \App\Livewire\DiscountForm::class)->name('admin.discounts.edit');

        Route::get('/admin/billings', \App\Livewire\BillingIndex::class)->name('admin.billings');
        Route::get('/admin/billings/create', \App\Livewire\BillingForm::class)->name('admin.billings.create');
        Route::get('/admin/payments/create', \App\Livewire\PaymentEntry::class)->name('admin.payments.create');
        Route::get('/admin/receipts/{billing}', [\App\Http\Controllers\ReceiptController::class, 'show'])->name('admin.receipts.show');
    });

    Route::middleware(['role:WALI_SANTRI'])->group(function () {
    });
});

require __DIR__.'/auth.php';
