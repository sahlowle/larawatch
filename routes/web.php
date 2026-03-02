<?php

use Illuminate\Support\Facades\Route;
use Sahlowle\Larawatch\Http\Controllers\DashboardController;
use Sahlowle\Larawatch\Livewire\Cache;
use Sahlowle\Larawatch\Livewire\Dashboard;
use Sahlowle\Larawatch\Livewire\Exceptions;
use Sahlowle\Larawatch\Livewire\Health;
use Sahlowle\Larawatch\Livewire\Jobs;
use Sahlowle\Larawatch\Livewire\Mail;
use Sahlowle\Larawatch\Livewire\Requests;
use Sahlowle\Larawatch\Livewire\ScheduledTasks;

/*
|--------------------------------------------------------------------------
| Larawatch Monitor Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the LarawatchServiceProvider within a group
| that has the configured prefix and middleware applied.
|
*/

Route::prefix(config('larawatch.path', 'monitor'))
    ->middleware(config('larawatch.middleware', ['web']))
    ->middleware('monitor.auth')
    ->group(function () {
        Route::get('/', Dashboard::class)->name('monitor.dashboard');
        Route::get('/requests', Requests::class)->name('monitor.requests');
        Route::get('/exceptions', Exceptions::class)->name('monitor.exceptions');
        Route::get('/jobs', Jobs::class)->name('monitor.jobs');
        Route::get('/health', Health::class)->name('monitor.health');
        Route::get('/cache', Cache::class)->name('monitor.cache');
        Route::get('/mail', Mail::class)->name('monitor.mail');
        Route::get('/scheduled-tasks', ScheduledTasks::class)->name('monitor.scheduled-tasks');

        // API endpoints
        Route::get('/api/chart-data', [DashboardController::class, 'chartData'])->name('monitor.api.chart');
    });
