<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

use App\Livewire\LandingPage;
use App\Livewire\Student\Dashboard;
use App\Livewire\Student\EditProfile;
use App\Livewire\Student\ExamPage;

Route::get('/', LandingPage::class)->name('home');

// Route Auth (Hanya bisa diakses tamu/guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// 3. Dashboard Peserta (Auth Required) - Hanya bisa diakses jika SUDAH login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('student.dashboard');
    Route::get('/exam/{packetId}', ExamPage::class)->name('exam.show');
    // TAMBAHKAN INI:
    Route::get('/student/profile', EditProfile::class)->name('student.profile');
});

// Logout (Opsional, nanti dipakai di dashboard)
Route::get('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');
