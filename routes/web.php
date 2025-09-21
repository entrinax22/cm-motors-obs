<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return Inertia::render('Welcome');
    })->name('home');

    Route::get('/services', function () {
        return Inertia::render('Services');
    })->name('services');

    Route::get('/contact-us', function () {
        return Inertia::render('Contact');
    })->name('contact');

    Route::post('/user/bookings/bookNow', [BookingController::class, 'bookNow'])->name('user.bookings.bookNow');
    Route::get('/user/services/selectList', [ServiceController::class, 'selectList'])->name('user.services.selectList');
});
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return Inertia::render('admin/Dashboard');
    })->name('dashboard');

    Route::get('/admin/dashboard/data', [DashboardController::class, 'index'])->name('admin.dashboard.data');
    Route::get('/admin/dashboard/generateReport', [DashboardController::class, 'generateReport'])->name('admin.dashboard.generateReport');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/bookings', [BookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services.index');

    Route::get('/admin/users/list', [UserController::class, 'list'])->name('admin.users.list');
    Route::get('/admin/users/selectList', [UserController::class, 'selectList'])->name('admin.users.selectList');
    Route::get('/admin/users/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/admin/users/update/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/delete/{id}', [UserController::class, 'destroy'])->name('admin.users.delete');

    Route::get('/admin/services/list', [ServiceController::class, 'list'])->name('admin.services.list');
    Route::get('/admin/services/selectList', [ServiceController::class, 'selectList'])->name('admin.services.selectList');
    Route::get('/admin/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/admin/services/store', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/admin/services/edit/{service_id}', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::post('/admin/services/update', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/admin/services/delete/{service_id}', [ServiceController::class, 'destroy'])->name('admin.services.delete');

    Route::get('/admin/bookings/list', [BookingController::class, 'list'])->name('admin.bookings.list');
    Route::get('/admin/bookings/create', [BookingController::class, 'create'])->name('admin.bookings.create');
    Route::post('/admin/bookings/store', [BookingController::class, 'store'])->name('admin.bookings.store');
    Route::get('/admin/bookings/edit/{booking_id}', [BookingController::class, 'edit'])->name('admin.bookings.edit');
    Route::post('/admin/bookings/update', [BookingController::class, 'update'])->name('admin.bookings.update');
    Route::delete('/admin/bookings/delete/{booking_id}', [BookingController::class, 'destroy'])->name('admin.bookings.delete');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
