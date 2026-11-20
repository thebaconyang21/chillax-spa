<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Chillax Spa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">
    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-ink/50 md:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-maroon text-white shadow-xl transition-transform duration-300 md:translate-x-0">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center border-b border-white/15 px-6 py-7 text-center">
            <span class="font-display text-5xl leading-none text-gold">Chillax Spa</span>
            <span class="mt-2 text-[10px] tracking-[0.18em] text-white/75">Relax &bull; Refresh &bull; Rejuvenate</span>
        </a>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5" aria-label="Main navigation">
            @php
                $menuItems = [
                    ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard'],
                    ['label' => 'Appointment Management', 'icon' => 'calendar', 'route' => 'appointments'],
                    ['label' => 'Customer Management', 'icon' => 'users', 'route' => 'customers'],
                    ['label' => 'Service Management', 'icon' => 'spa', 'route' => 'services'],
                    ['label' => 'Therapist & Commission', 'icon' => 'person', 'route' => 'therapists'],
                    ['label' => 'Point of Sale (POS)', 'icon' => 'cart', 'route' => 'pos'],
                    ['label' => 'Transaction History', 'icon' => 'receipt', 'route' => 'transactions'],
                    ['label' => 'Reports', 'icon' => 'chart'],
                    ['label' => 'User Management', 'icon' => 'settings'],
                ];
            @endphp

            @foreach ($menuItems as $item)
                @php
                    $isActive = isset($item['route']) && request()->routeIs($item['route']);
                @endphp
                <a href="{{ isset($item['route']) ? route($item['route']) : '#' }}"
                    @if ($isActive) aria-current="page" @endif
                    class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 text-sm transition {{ $isActive ? 'bg-gold font-semibold text-maroon shadow-sm' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        @switch($item['icon'])
                            @case('dashboard')
                                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                                @break
                            @case('calendar')
                                <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01"/>
                                @break
                            @case('users')
                                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6M23 11h-6"/>
                                @break
                            @case('spa')
                                <path d="M12 21c-4-3-7-6-7-10a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 4-3 7-7 10Z"/><path d="M12 9c-2-3-2-5 0-7 2 2 2 4 0 7ZM5 13c-2-1-3-3-3-5 3 0 5 1 6 4M19 13c2-1 3-3 3-5-3 0-5 1-6 4"/>
                                @break
                            @case('person')
                                <circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0M19 8h3M20.5 6.5v3"/>
                                @break
                            @case('cart')
                                <path d="M3 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/>
                                @break
                            @case('receipt')
                                <path d="M5 3h14v18l-3-2-4 2-4-2-3 2V3Z"/><path d="M9 8h6M9 12h6M9 16h3"/>
                                @break
                            @case('chart')
                                <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>
                                @break
                            @case('settings')
                                <circle cx="12" cy="12" r="3"/><path d="m19.4 15 .1.1a1.7 1.7 0 1 1-2.4 2.4l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a1.7 1.7 0 1 1-3.4 0v-.2a1.7 1.7 0 0 0-2.9-1.2l-.1.1a1.7 1.7 0 1 1-2.4-2.4l.1-.1a1.7 1.7 0 0 0-1.2-2.9H4a1.7 1.7 0 1 1 0-3.4h.2a1.7 1.7 0 0 0 1.2-2.9l-.1-.1a1.7 1.7 0 1 1 2.4-2.4l.1.1a1.7 1.7 0 0 0 2.9-1.2V4a1.7 1.7 0 1 1 3.4 0v.2a1.7 1.7 0 0 0 2.9 1.2l.1-.1a1.7 1.7 0 1 1 2.4 2.4l-.1.1a1.7 1.7 0 0 0 1.2 2.9h.2a1.7 1.7 0 1 1 0 3.4h-.2a1.7 1.7 0 0 0-1.2.9Z"/>
                                @break
                        @endswitch
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="border-t border-white/15 p-3">
            <a href="#" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/85 transition hover:bg-white/10 hover:text-white">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M21 19V5"/></svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="min-h-screen md:pl-72">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-maroon/10 bg-white/90 px-4 backdrop-blur sm:px-7">
            <button id="sidebar-toggle" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-ink transition hover:bg-maroon/5 focus:outline-none focus:ring-2 focus:ring-gold md:hidden" aria-label="Open navigation" aria-expanded="false" aria-controls="sidebar">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="hidden text-sm text-ink/60 sm:block">@yield('breadcrumb', 'Spa Management')</div>
            <div class="ml-auto flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-semibold">Jane Dela Cruz</p>
                    <p class="text-xs text-ink/60">Administrator</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-maroon/10 text-sm font-semibold text-maroon" aria-hidden="true">JD</div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-[1600px] p-4 sm:p-7 lg:p-9">
            @yield('content')
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function setSidebarOpen(isOpen) {
            sidebar.classList.toggle('-translate-x-full', !isOpen);
            sidebarOverlay.classList.toggle('hidden', !isOpen);
            sidebarToggle.setAttribute('aria-expanded', String(isOpen));
        }

        sidebarToggle.addEventListener('click', () => {
            setSidebarOpen(sidebar.classList.contains('-translate-x-full'));
        });
        sidebarOverlay.addEventListener('click', () => setSidebarOpen(false));
    </script>
</body>
</html>
