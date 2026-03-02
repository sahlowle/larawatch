<?php

namespace Sahlowle\Larawatch\Livewire;

use Livewire\Component;
use Sahlowle\Larawatch\Services\CacheMonitorService;

class Cache extends Component
{
    public function getCacheStatsProperty(): array
    {
        return app(CacheMonitorService::class)->getLatestStats();
    }

    public function render()
    {
        return view('larawatch::livewire.monitor.cache')
            ->layout('larawatch::monitor.layouts.app', ['title' => 'Cache & Memory']);
    }
}
