<?php

namespace Sahlowle\Larawatch;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Sahlowle\Larawatch\Commands\PruneMonitorDataCommand;
use Sahlowle\Larawatch\Http\Middleware\MonitorAuthMiddleware;
use Sahlowle\Larawatch\Http\Middleware\MonitorRequestMiddleware;
use Sahlowle\Larawatch\Livewire\Cache;
use Sahlowle\Larawatch\Livewire\Dashboard;
use Sahlowle\Larawatch\Livewire\Exceptions;
use Sahlowle\Larawatch\Livewire\Health;
use Sahlowle\Larawatch\Livewire\Jobs;
use Sahlowle\Larawatch\Livewire\Mail;
use Sahlowle\Larawatch\Livewire\Requests;
use Sahlowle\Larawatch\Livewire\ScheduledTasks;
use Sahlowle\Larawatch\Services\CacheMonitorService;
use Sahlowle\Larawatch\Services\ExceptionMonitorService;
use Sahlowle\Larawatch\Services\HealthCheckService;
use Sahlowle\Larawatch\Services\JobMonitorService;
use Sahlowle\Larawatch\Services\MailMonitorService;
use Sahlowle\Larawatch\Services\RequestMonitorService;
use Sahlowle\Larawatch\Services\ScheduledTaskMonitorService;
use Sahlowle\Larawatch\Services\StatsAggregatorService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LarawatchServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('larawatch')
            ->hasConfigFile('larawatch')
            ->hasMigrations([
                'create_monitor_requests_table',
                'create_monitor_exceptions_table',
                'create_monitor_jobs_table',
                'create_monitor_health_checks_table',
                'create_monitor_cache_stats_table',
                'add_user_to_monitor_exceptions_table',
                'create_monitor_mails_table',
                'create_monitor_scheduled_tasks_table',
            ])
            ->hasRoutes(['web'])
            ->hasViews('larawatch')
            ->hasCommand(PruneMonitorDataCommand::class);
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(RequestMonitorService::class);
        $this->app->singleton(ExceptionMonitorService::class);
        $this->app->singleton(JobMonitorService::class);
        $this->app->singleton(HealthCheckService::class);
        $this->app->singleton(CacheMonitorService::class);
        $this->app->singleton(StatsAggregatorService::class);
        $this->app->singleton(MailMonitorService::class);
        $this->app->singleton(ScheduledTaskMonitorService::class);
    }

    public function bootingPackage(): void
    {
        $this->registerGate();
        $this->registerMiddleware();
        $this->registerLivewireComponents();



        if (! config('larawatch.enabled')) {
            return;
        }

        $this->registerQueueListeners();
        $this->registerMailListeners();
        $this->registerSchedulerListeners();
    }

    /**
     * Register the viewMonitor authorization gate.
     */
    protected function registerGate(): void
    {
        Gate::define('viewMonitor', function ($user) {

            if (app()->environment('local')) {
                return true;
            }

            $allowedEmails = config('larawatch.allowed_emails', []);
            if (! empty($allowedEmails) && in_array($user->email, $allowedEmails)) {
                return true;
            }

            $allowedRoles = config('larawatch.allowed_roles', []);
            if (! empty($allowedRoles) && method_exists($user, 'hasRole')) {
                foreach ($allowedRoles as $role) {
                    if ($user->hasRole($role)) {
                        return true;
                    }
                }
            }

            return false;
        });
    }

    /**
     * Register the middleware aliases.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        $router->aliasMiddleware('monitor.auth', MonitorAuthMiddleware::class);

        if (config('larawatch.enabled') && config('larawatch.features.requests', true)) {
            $router->pushMiddlewareToGroup('web', MonitorRequestMiddleware::class);
        }
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('larawatch::dashboard', Dashboard::class);
        Livewire::component('larawatch::requests', Requests::class);
        Livewire::component('larawatch::exceptions', Exceptions::class);
        Livewire::component('larawatch::jobs', Jobs::class);
        Livewire::component('larawatch::health', Health::class);
        Livewire::component('larawatch::cache', Cache::class);
        Livewire::component('larawatch::mail', Mail::class);
        Livewire::component('larawatch::scheduled-tasks', ScheduledTasks::class);
    }

    /**
     * Register mail event listeners for mail monitoring.
     */
    protected function registerMailListeners(): void
    {
        if (! config('larawatch.features.mail', true)) {
            return;
        }

        Event::listen(MessageSent::class, function (MessageSent $event) {
            app(MailMonitorService::class)->recordSent($event);
        });
    }

    /**
     * Register queue event listeners for job monitoring.
     */
    protected function registerQueueListeners(): void
    {
        if (! config('larawatch.features.jobs', true)) {
            return;
        }

        Event::listen(JobProcessing::class, function (JobProcessing $event) {
            app(JobMonitorService::class)->recordProcessing($event);
        });

        Event::listen(JobProcessed::class, function (JobProcessed $event) {
            app(JobMonitorService::class)->recordProcessed($event);
        });

        Event::listen(JobFailed::class, function (JobFailed $event) {
            app(JobMonitorService::class)->recordFailed($event);
        });
    }

    /**
     * Register scheduled task event listeners.
     */
    protected function registerSchedulerListeners(): void
    {
        if (! config('larawatch.features.scheduled_tasks', true)) {
            return;
        }

        Event::listen(ScheduledTaskStarting::class, function (ScheduledTaskStarting $event) {
            app(ScheduledTaskMonitorService::class)->recordStarting($event->task);
        });

        Event::listen(ScheduledTaskFinished::class, function (ScheduledTaskFinished $event) {
            app(ScheduledTaskMonitorService::class)->recordFinished($event->task);
        });

        Event::listen(ScheduledTaskFailed::class, function (ScheduledTaskFailed $event) {
            app(ScheduledTaskMonitorService::class)->recordFailed($event->task);
        });

        Event::listen(ScheduledTaskSkipped::class, function (ScheduledTaskSkipped $event) {
            app(ScheduledTaskMonitorService::class)->recordSkipped($event->task);
        });
    }
}
