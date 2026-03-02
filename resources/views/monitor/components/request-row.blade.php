@props([
    'request' => null,
    'method' => '',
    'path' => '',
    'statusCode' => 200,
    'controller' => '',
    'duration' => 0,
])

@php
    if ($request) {
        $method = $request->method;
        $path = $request->path;
        $statusCode = $request->status_code;
        $controller = $request->controller_action;
        $duration = $request->duration_ms;
    }

    $statusClass = $statusCode >= 500 ? 'status-5xx' : ($statusCode >= 400 ? 'status-4xx' : 'status-2xx');
    $durationColor = $duration > 1000 ? 'color:var(--red)' : ($duration > 400 ? 'color:var(--yellow)' : '');
    $formattedDuration = $duration > 999 ? number_format($duration / 1000, 2) . 's' : $duration . 'ms';

    $shortController = $controller
        ? str_replace('Controller@', '@', str_replace('App\\Http\\Controllers\\', '', $controller))
        : '-';
@endphp

<tr>
    <td><span class="method-badge method-{{ $method }}">{{ $method }}</span></td>
    <td><span class="route-path">{{ $path }}</span></td>
    <td><span class="status-code {{ $statusClass }}">{{ $statusCode }}</span></td>
    <td style="font-size:11px;color:var(--text-muted);font-family:'JetBrains Mono',monospace;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
        title="{{ $controller }}">
        {{ $shortController }}</td>
    <td class="duration" style="{{ $durationColor }}">{{ $formattedDuration }}</td>
</tr>
