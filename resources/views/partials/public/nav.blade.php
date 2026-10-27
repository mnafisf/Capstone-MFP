@php
    $activeSection = $activeSection ?? 'beranda';
@endphp

<header
    class="sticky top-0 z-50 border-b border-blue-800/30 bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 shadow-lg"
    x-data="{ mobileOpen: false }"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="SAPHYR home">
            <x-application-logo class="h-9 w-9 text-blue-300" />
                    <span class="text-2xl font-black tracking-[-0.08em] text-white" style="font-style: italic;">SAPHYR</span>
        </a>

        <nav class="hidden items-center gap-6 lg:flex">
            <a href="{{ route('home') }}" class="home-nav-link {{ $activeSection === 'beranda' ? 'home-nav-link-active' : '' }} text-white hover:text-blue-200">Beranda</a>
            <a href="{{ route('public.events.index') }}" class="home-nav-link {{ $activeSection === 'event' ? 'home-nav-link-active' : '' }} text-white hover:text-blue-200">Event</a>
            <a href="{{ route('public.teams.index') }}" class="home-nav-link {{ $activeSection === 'tim' ? 'home-nav-link-active' : '' }} text-white hover:text-blue-200">Tim</a>
            <a href="{{ route('public.standings.index') }}" class="home-nav-link {{ $activeSection === 'klasemen' ? 'home-nav-link-active' : '' }} text-white hover:text-blue-200">Klasemen</a>
        </nav>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="home-btn-primary !hidden !px-5 !py-2 !text-xs sm:!inline-flex bg-white text-blue-900 hover:bg-blue-50">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="home-btn-primary !hidden !px-5 !py-2 !text-xs sm:!inline-flex bg-white text-blue-900 hover:bg-blue-50 {{ $activeSection === 'login' ? 'ring-2 ring-blue-300 ring-offset-2 ring-offset-blue-900' : '' }}">
                    Masuk Akun
                </a>
            @endauth

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-blue-700/50 text-white hover:bg-blue-800/50 lg:hidden"
                @click="mobileOpen = !mobileOpen"
                aria-label="Menu"
            >
                <svg x-show="!mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-show="mobileOpen" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak x-transition class="border-t border-blue-800/50 bg-blue-900/95 px-4 py-3 lg:hidden">
        <nav class="flex flex-col gap-1">
            <a href="{{ route('home') }}" class="home-mobile-link text-white hover:text-blue-200">Beranda</a>
            <a href="{{ route('public.events.index') }}" class="home-mobile-link text-white hover:text-blue-200">Event</a>
            <a href="{{ route('public.teams.index') }}" class="home-mobile-link text-white hover:text-blue-200">Tim</a>
            <a href="{{ route('public.standings.index') }}" class="home-mobile-link text-white hover:text-blue-200">Klasemen</a>
            @auth
                <a href="{{ route('dashboard') }}" class="home-mobile-link mt-2 border-t border-blue-800/50 pt-2 text-white hover:text-blue-200">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="home-mobile-link mt-2 border-t border-blue-800/50 pt-2 text-blue-300 hover:text-blue-100">Masuk Akun</a>
            @endauth
        </nav>
    </div>
</header>
