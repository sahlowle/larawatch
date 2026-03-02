<?php

namespace Sahlowle\Larawatch\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Sahlowle\Larawatch\Models\MonitorException;

class Exceptions extends Component
{
    use WithPagination;

    public function render()
    {
        return view('larawatch::livewire.monitor.exceptions', [
            'exceptions' => MonitorException::orderByDesc('last_occurred_at')->paginate(50),
        ])->layout('larawatch::monitor.layouts.app', ['title' => 'Exceptions']);
    }
}
