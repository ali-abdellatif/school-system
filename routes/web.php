<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\ApplicationStatus;
use App\Livewire\PublicApplicationForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// طلبات القبول العامة (الموقع العام)
Route::get('/apply', PublicApplicationForm::class)->name('apply');
Route::get('/application-status', ApplicationStatus::class)->name('application.status');

Route::redirect('/dashboard', '/admin')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
