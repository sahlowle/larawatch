<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Larawatch Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable the entire monitoring system. When disabled, no data
    | is recorded and the dashboard is inaccessible.
    |
    */
    'enabled' => env('MONITOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | The default color theme for the monitor dashboard. Users can toggle
    | between themes via the UI, which persists their preference in
    | localStorage. This setting controls the initial default.
    |
    | Supported: "dark", "light"
    |
    */
    'theme' => env('MONITOR_THEME', 'light'),

    /*
    |--------------------------------------------------------------------------
    | Route Path Prefix
    |--------------------------------------------------------------------------
    |
    | The URI prefix under which the monitor dashboard will be accessible.
    | For example, "monitor" means the dashboard is at /monitor.
    |
    */
    'path' => env('MONITOR_PATH', 'monitor'),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Optionally use a separate database connection for all monitor tables.
    | Set to null to use the application's default connection.
    |
    */
    'connection' => env('MONITOR_DB_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | All monitor tables will be prefixed with this value.
    | Default is 'monitor_'. Change to customize table names.
    |
    */
    'table_prefix' => env('MONITOR_TABLE_PREFIX', 'monitor_'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware stack applied to all monitor routes.
    |
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Control who can access the monitor dashboard.
    | - allowed_emails: Array of email addresses that can access the monitor.
    | - allowed_roles: Array of role names (requires a role system on User model).
    | In "local" environment, all authenticated users have access by default.
    |
    */
    'allowed_emails' => [
        // 'admin@example.com',
    ],

    'allowed_roles' => [
        // 'admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles
    |--------------------------------------------------------------------------
    |
    | Enable or disable individual monitoring modules. Disabled modules
    | won't record data and their nav items will be hidden.
    |
    */
    'features' => [
        'requests' => true,
        'exceptions' => true,
        'jobs' => true,
        'health' => true,
        'cache' => true,
        'mail' => true,
        'logs' => true,
        'scheduled_tasks' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Monitoring
    |--------------------------------------------------------------------------
    */
    'request' => [
        'ignore_paths' => ['monitor*', '_debugbar*', 'telescope*', 'horizon*', 'livewire*'],
        'ignore_status_codes' => [],
        'slow_threshold_ms' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Checks
    |--------------------------------------------------------------------------
    */
    'health' => [
        'checks' => ['database', 'redis', 'queue', 'storage', 'mail', 'scheduler'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Retention & Pruning
    |--------------------------------------------------------------------------
    |
    | Configure how long to keep each type of monitoring data.
    |
    */
    'data_retention' => [
        'requests' => ['keep_hours' => 1,  'prune_every' => 'hourly'],
        'exceptions' => ['keep_days' => 30, 'prune_every' => 'daily'],
        'jobs' => ['keep_days' => 7,  'prune_every' => 'daily'],
        'health_checks' => ['keep_days' => 7,  'prune_every' => 'daily'],
        'cache_stats' => ['keep_days' => 7,  'prune_every' => 'daily'],
        'mails' => ['keep_days' => 30, 'prune_every' => 'daily'],
        'scheduled_tasks' => ['keep_days' => 14, 'prune_every' => 'daily'],
    ],

];
