<?php

namespace Sahlowle\Larawatch\Livewire;

use Livewire\Component;
use Sahlowle\Larawatch\Services\HealthCheckService;

class Health extends Component
{
    public function runChecks(): array
    {
        return app(HealthCheckService::class)->runChecks();
    }

    public function getHealthChecksProperty(): array
    {
        return $this->runChecks();
    }

    public function render()
    {
        return view('larawatch::livewire.monitor.health')
            ->layout('larawatch::monitor.layouts.app', ['title' => 'Health Checks']);
    }
}
