<?php

namespace Sahlowle\Larawatch\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Sahlowle\Larawatch\Models\MonitorRequest;

class Requests extends Component
{
    use WithPagination;

    public function render()
    {
        return view('larawatch::livewire.monitor.requests', [
            'requests' => MonitorRequest::orderByDesc('created_at')->paginate(50),
        ])->layout('larawatch::monitor.layouts.app', ['title' => 'Requests']);
    }
}
