<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
});

require __DIR__.'/auth.php';
