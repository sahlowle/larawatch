@props(['title' => 'Dashboard', 'hasLiveMode' => false])

<div class="topbar">
    <div class="topbar-title">{{ $title }}</div>
    <span class="topbar-meta" x-data="{ ago: 0 }" x-init="setInterval(() => ago++, 1000)"
        x-text="ago < 5 ? 'Updated just now' : 'Updated ' + ago + 's ago'">Updated just now</span>

    {{-- Theme Toggle --}}
    <button class="btn btn-ghost" @click="theme = theme === 'dark' ? 'light' : 'dark'"
        :title="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'" style="padding:8px 10px;">
        <template x-if="theme === 'dark'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" style="width:16px;height:16px;">
                <circle cx="12" cy="12" r="5" />
                <line x1="12" y1="1" x2="12" y2="3" />
                <line x1="12" y1="21" x2="12" y2="23" />
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                <line x1="1" y1="12" x2="3" y2="12" />
                <line x1="21" y1="12" x2="23" y2="12" />
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
            </svg>
        </template>
        <template x-if="theme === 'light'">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" style="width:16px;height:16px;">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
            </svg>
        </template>
    </button>

    <button class="btn btn-ghost"
        onclick="if(window.Livewire){Livewire.dispatch('$refresh')}else{window.location.reload()}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <polyline points="23 4 23 10 17 10" />
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
        </svg>
        Refresh
    </button>
    @if ($hasLiveMode ?? false)
        <button class="btn btn-primary" wire:click="toggleLiveMode" style="position:relative;overflow:hidden;">
            <span style="display:inline-flex;align-items:center;gap:6px;">
                <span
                    style="width:7px;height:7px;border-radius:50%;background:rgba(255,255,255,.9);display:inline-block;animation:pulse-dot 1.5s ease-in-out infinite;box-shadow:0 0 6px rgba(255,255,255,.6);"></span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" style="width:14px;height:14px;">
                    <path d="M18 20V10" />
                    <path d="M12 20V4" />
                    <path d="M6 20v-6" />
                </svg>
                Live Mode
            </span>
        </button>
    @endif
</div>
