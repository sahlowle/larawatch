<?php

namespace Sahlowle\Larawatch\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Sahlowle\Larawatch\Models\MonitorMail;

class Mail extends Component
{
    use WithPagination;

    public function render()
    {
        $mails = MonitorMail::orderByDesc('created_at')->paginate(50);
        $sentToday = MonitorMail::whereDate('created_at', today())->where('status', 'sent')->count();
        $failedToday = MonitorMail::whereDate('created_at', today())->where('status', 'failed')->count();

        return view('larawatch::livewire.monitor.mail', [
            'mails' => $mails,
            'sentToday' => $sentToday,
            'failedToday' => $failedToday,
        ])->layout('larawatch::monitor.layouts.app', ['title' => 'Mail Monitor']);
    }
}
