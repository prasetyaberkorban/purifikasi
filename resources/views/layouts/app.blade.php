<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0D4C1E">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('img/purifikasi-logo.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-canvas" x-data="{ sidebarOpen: false }">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:rounded-lg focus:bg-forest focus:px-4 focus:py-2 focus:text-white">
        Lewati ke konten
    </a>

    {{-- Overlay drawer mobile --}}
    <div x-show="sidebarOpen" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-forest-900/50 lg:hidden"
         @click="sidebarOpen = false" aria-hidden="true"></div>

    {{-- Sidebar (desktop + drawer mobile) --}}
    <aside id="app-sidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-[17.5rem] max-w-[85vw] -translate-x-full flex-col bg-gradient-to-b from-forest via-forest to-forest-900 transition-transform duration-300 ease-out lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           aria-label="Navigasi utama">
        <div class="flex items-center justify-between gap-2 px-5 py-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('img/purifikasi-logo.png') }}" alt="Purifikasi" class="h-11 w-11 rounded-xl bg-white/95 p-1 object-contain shadow-sm">
                <span class="block text-lg font-extrabold tracking-[0.12em] text-white">PURIFIKASI</span>
            </a>
            <button type="button" class="rounded-lg p-2 text-white/70 hover:bg-white/10 lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
                <x-ui-icon name="x" />
            </button>
        </div>

        <nav class="no-scrollbar flex-1 overflow-y-auto px-3 pb-4">
            <ul class="space-y-1">
                @foreach ($menu() as $item)
                    @php
                        $isActive = collect($item['active'])->contains(fn ($pattern) => request()->routeIs($pattern));
                        $hasChildren = ! empty($item['children']);
                    @endphp
                    <li x-data="{ open: {{ $hasChildren && $isActive ? 'true' : 'false' }} }">
                        @if ($hasChildren)
                            <button type="button" @click="open = !open"
                                    class="nav-link w-full justify-between {{ $isActive ? 'text-white' : '' }}"
                                    :aria-expanded="open.toString()" aria-controls="submenu-{{ $item['key'] }}">
                                <span class="flex items-center gap-3">
                                    <x-ui-icon :name="$item['icon']" class="h-[1.15rem] w-[1.15rem]" />
                                    {{ $item['label'] }}
                                </span>
                                <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <ul id="submenu-{{ $item['key'] }}" x-show="open" x-collapse class="mt-1 space-y-1 pl-4">
                                @foreach ($item['children'] as $child)
                                    @php
                                        $hasSubChildren = ! empty($child['children']);
                                        $childActive = ! empty($child['is_active']) || (is_array($child['active'] ?? null) && collect($child['active'])->contains(fn ($p) => request()->routeIs($p)));
                                    @endphp
                                    @if ($hasSubChildren)
                                        <li x-data="{ openSub: {{ $childActive ? 'true' : 'false' }} }">
                                            <button type="button" @click="openSub = !openSub"
                                                    class="nav-link w-full justify-between py-1.5 text-[0.8rem] {{ $childActive ? 'text-white font-bold' : '' }}">
                                                <span class="flex items-center gap-2">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                                                    {{ $child['label'] }}
                                                </span>
                                                <svg class="h-3.5 w-3.5 transition-transform" :class="openSub ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>
                                            <ul x-show="openSub" x-collapse class="mt-1 space-y-1 pl-3 border-l border-white/10 ml-2">
                                                @foreach ($child['children'] as $sub)
                                                    @php
                                                        $subActive = ! empty($sub['is_active']) || (is_array($sub['active'] ?? null) && collect($sub['active'])->contains(fn ($p) => request()->routeIs($p)));
                                                    @endphp
                                                    <li>
                                                        <a href="{{ $sub['route'] }}"
                                                           class="nav-link py-1.5 text-[0.76rem] {{ $subActive ? 'nav-link-active' : '' }}">
                                                            <span class="h-1 w-1 rounded-full bg-current opacity-60"></span>
                                                            {{ $sub['label'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{ $child['route'] }}"
                                               class="nav-link py-2 text-[0.83rem] {{ (!empty($child['is_active']) || request()->routeIs($child['active'])) ? 'nav-link-active' : '' }}">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current opacity-60"></span>
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <a href="{{ $item['route'] }}" class="nav-link {{ $isActive ? 'nav-link-active' : '' }}"
                               @if (!empty($item['external'])) target="_blank" rel="noopener noreferrer" @endif>
                                <span class="flex items-center gap-3">
                                    <x-ui-icon :name="$item['icon']" class="h-[1.15rem] w-[1.15rem]" />
                                    {{ $item['label'] }}
                                </span>
                                @if (!empty($item['external']))
                                    <x-ui-icon name="arrow-up-right" class="h-3.5 w-3.5 opacity-60 ml-auto" />
                                @endif
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="border-t border-white/10 px-4 py-4">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-9 w-9 rounded-full object-cover ring-2 ring-white/20">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[0.7rem] text-white/55">{{ auth()->user()->role->label() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-white/60 hover:bg-white/10 hover:text-white" aria-label="Keluar">
                        <x-ui-icon name="logout" class="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:pl-[17.5rem]">
        {{-- Header --}}
        <header class="sticky top-0 z-30 border-b border-line bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/75">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
                <button type="button" class="rounded-xl border border-line p-2 text-forest lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                    <x-ui-icon name="menu" />
                </button>

                <img src="{{ asset('img/purifikasi-logo.png') }}" alt="Purifikasi"
                     class="h-8 w-8 shrink-0 object-contain lg:hidden">

                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-base font-bold text-forest sm:text-lg">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="truncate text-[0.72rem] text-ink-soft sm:text-xs">{{ $subtitle }}</p>
                    @endif
                </div>

                <a href="{{ route('notifications') }}" class="relative rounded-xl border border-line p-2.5 text-ink-soft hover:border-leaf/40 hover:text-forest" aria-label="Notifikasi">
                    <x-ui-icon name="bell" class="h-[1.1rem] w-[1.1rem]" />
                    @if (($notificationCount = \App\Models\Asset::query()->whereRaw('(quantity - borrowed_quantity) <= minimum_stock')->count()) > 0)
                        <span class="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[0.6rem] font-bold text-white">{{ $notificationCount }}</span>
                    @endif
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-xl border border-line py-1.5 pr-3 pl-1.5 hover:border-leaf/40">
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-8 w-8 rounded-lg object-cover">
                    <span class="hidden leading-tight sm:block">
                        <span class="block max-w-[9rem] truncate text-xs font-semibold text-ink">{{ auth()->user()->name }}</span>
                        <span class="block text-[0.65rem] text-ink-soft">{{ auth()->user()->role->label() }}</span>
                    </span>
                </a>
            </div>
        </header>

        <main id="main-content" class="mx-auto w-full max-w-[92rem] px-4 pt-5 pb-10 sm:px-6 lg:pb-12">
            @if (session('status'))
                <div class="mb-4 rounded-xl border border-leaf/30 bg-leaf-50 px-4 py-3 text-sm font-medium text-forest">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-semibold">Periksa kembali data berikut:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="hidden border-t border-line px-6 py-4 text-center text-[0.7rem] text-ink-soft lg:block">
            &copy; {{ now()->year }} PT. Purifikasi Andalan Diponegoro
        </footer>
    </div>

    <x-delete-modal />

    @livewireScripts
</body>
</html>