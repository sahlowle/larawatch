<?php

namespace Sahlowle\Larawatch\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Sahlowle\Larawatch\Models\MonitorScheduledTask;
use Sahlowle\Larawatch\Services\ScheduledTaskMonitorService;

class ScheduledTasks extends Component
{
    use WithPagination;

    public function render()
    {
        $service = app(ScheduledTaskMonitorService::class);
        $registeredTasks = $service->getRegisteredTasks();

        return view('larawatch::livewire.monitor.scheduled-tasks', [
            'registeredTasks' => $registeredTasks,
            'taskHistory' => MonitorScheduledTask::orderByDesc('created_at')->paginate(30),
        ])->layout('larawatch::monitor.layouts.app', ['title' => 'Scheduled Tasks']);
    }
}
