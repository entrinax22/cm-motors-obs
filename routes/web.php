<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    Route::get('/', function () {
        return Inertia::render('Welcome');
    })->name('home');

    Route::get('/services', function () {
        return Inertia::render('Services');
    })->name('services');

    Route::get('/contact-us', function () {
        return Inertia::render('Contact');
    })->name('contact');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
