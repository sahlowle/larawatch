<?php

namespace Sahlowle\Larawatch\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Sahlowle\Larawatch\Models\MonitorJob;

class Jobs extends Component
{
    use WithPagination;

    public function render()
    {
        return view('larawatch::livewire.monitor.jobs', [
            'jobs' => MonitorJob::orderByDesc('created_at')->paginate(50),
        ])->layout('larawatch::monitor.layouts.app', ['title' => 'Queue Jobs']);
    }
}
