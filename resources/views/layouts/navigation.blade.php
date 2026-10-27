@php
    $role = optional(auth()->user()->role)->slug;
    $roleName = optional(auth()->user()->role)->name ?? 'Tanpa Peran';

    $panelSubtitle = match ($role) {
        'super-admin' => 'Panel Super Admin',
        'admin' => 'Panel Admin Panitia',
        'club-admin' => 'Panel Admin Klub',
        default => 'Manajer Kompetisi',
    };

    $linkActive = fn (bool $active): string => $active ? 'is-active' : '';

    $masterDataActive = $role === 'super-admin' && (
        request()->routeIs('super-admin.admins.*')
        || request()->routeIs('super-admin.club-admins.*')
        || request()->routeIs('super-admin.stadiums.*')
        || request()->routeIs('super-admin.referees.*')
        || request()->routeIs('super-admin.transfers.verification.*')
        || request()->routeIs('super-admin.settings.*')
    );

    $operationalActive = in_array($role, ['admin', 'super-admin'], true) && (
        request()->routeIs('admin.events.*')
        || request()->routeIs('admin.matches.*')
        || request()->routeIs('admin.verification.*')
        || request()->routeIs('admin.officials.verification.*')
        || request()->routeIs('admin.club-completeness.*')
    );

    $clubMenuActive = $role === 'club-admin' && (
        request()->routeIs('club-admin.players.*')
        || request()->routeIs('club-admin.officials.*')
        || request()->routeIs('club-admin.club.colors.*')
        || request()->routeIs('club-admin.events.*')
        || request()->routeIs('club-admin.lineups.*')
        || request()->routeIs('club-admin.transfers.*')
    );

    $operationalSectionKey = $role === 'admin' ? 'menu-utama' : 'operasional-panitia';
    $operationalSectionLabel = $role === 'admin' ? 'Menu Utama' : 'Operasional Panitia';

    $activeSection = match (true) {
        $masterDataActive => 'master-data',
        $operationalActive => $operationalSectionKey,
        $clubMenuActive => 'menu-klub',
        default => null,
    };

    // Saat sidebar direndahkan (icon only), klik judul section membuka sidebar lagi.
    $sectionToggle = fn (string $key): string => "desktopOpen ? toggleSection('{$key}') : openSection('{$key}')";
@endphp

<aside
    data-active-section="{{ $activeSection }}"
    class="sidebar-panel"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <div class="shrink-0 border-b border-blue-500/40 px-3 py-5 lg:px-5">
        <div class="flex items-center justify-between gap-2">
            <a
                href="{{ route('dashboard') }}"
                @click="sidebarOpen = false"
                class="flex min-w-0 flex-1 items-center gap-2.5"
                title="Dasbor SAPHYR"
            >
                <x-application-logo class="sidebar-brand-logo h-9 w-9 shrink-0 text-white" />
                <span class="sidebar-text min-w-0">
                    <span class="block truncate text-xl font-black tracking-wide text-white" style="font-style: italic;">SAPHYR</span>
                    <span class="mt-1 block truncate text-xs uppercase tracking-widest text-blue-200">{{ $panelSubtitle }}</span>
                </span>
            </a>
            <button
                type="button"
                @click="toggleDesktopSidebar()"
                class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white hover:bg-white/20 lg:inline-flex"
                aria-label="Sembunyikan sidebar"
                title="Sembunyikan sidebar"
            >
                <svg class="sidebar-toggle-collapse h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <svg class="sidebar-toggle-expand h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
        <p class="sidebar-text mt-3 truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
        <p class="sidebar-text text-[10px] uppercase tracking-wide text-blue-300">{{ $roleName }}</p>
    </div>

    <nav class="sidebar-nav flex-1 overflow-y-auto overflow-x-hidden pb-2 pt-2">
        <a
            href="{{ route('dashboard') }}"
            @click="sidebarOpen = false"
            title="Beranda"
            class="sidebar-link {{ $linkActive(request()->routeIs('dashboard')) }}"
        >
            <x-nav-icon name="home" class="h-5 w-5 opacity-90" />
            <span class="sidebar-text">Beranda</span>
        </a>

        @if ($role === 'super-admin')
            <button
                type="button"
                @click="{!! $sectionToggle('master-data') !!}"
                class="sidebar-section-header {{ $masterDataActive ? 'is-active' : '' }}"
                :aria-expanded="sectionStates['master-data']"
                title="Master Data"
            >
                <span class="flex items-center gap-2.5">
                    <x-nav-icon name="database" class="h-4 w-4" />
                    <span class="sidebar-text">Master Data</span>
                </span>
                <svg class="sidebar-section-chevron" :class="sectionStates['master-data'] ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="sectionStates['master-data']" x-cloak class="sidebar-section-content">
                <a href="{{ route('super-admin.admins.index') }}" @click="sidebarOpen = false" title="Admin Panitia" class="sidebar-link {{ $linkActive(request()->routeIs('super-admin.admins.*')) }}">
                    <x-nav-icon name="users" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Admin Panitia</span>
                </a>
                <a href="{{ route('super-admin.club-admins.index') }}" @click="sidebarOpen = false" title="Admin Klub" class="sidebar-link {{ $linkActive(request()->routeIs('super-admin.club-admins.*')) }}">
                    <x-nav-icon name="id-card" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Admin Klub</span>
                </a>
                <a href="{{ route('super-admin.stadiums.index') }}" @click="sidebarOpen = false" title="Stadion" class="sidebar-link {{ $linkActive(request()->routeIs('super-admin.stadiums.*')) }}">
                    <x-nav-icon name="map-pin" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Stadion</span>
                </a>
                <a href="{{ route('super-admin.referees.index') }}" @click="sidebarOpen = false" title="Wasit" class="sidebar-link {{ $linkActive(request()->routeIs('super-admin.referees.*')) }}">
                    <x-nav-icon name="shield-check" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Wasit</span>
                </a>
                <a href="{{ route('super-admin.transfers.verification.index') }}" @click="sidebarOpen = false" title="Verifikasi Mutasi" class="sidebar-link {{ $linkActive(request()->routeIs('super-admin.transfers.verification.*')) }}">
                    <x-nav-icon name="swap" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Verifikasi Mutasi</span>
                </a>
                <a href="{{ route('super-admin.settings.index') }}" @click="sidebarOpen = false" title="Pengaturan Lineup Pertandingan" class="sidebar-link {{ $linkActive(request()->routeIs('super-admin.settings.*')) }}">
                    <x-nav-icon name="sliders" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Pengaturan Lineup Pertandingan</span>
                </a>
            </div>
        @endif

        @if (in_array($role, ['admin', 'super-admin'], true))
            <button
                type="button"
                @click="{!! $sectionToggle($operationalSectionKey) !!}"
                class="sidebar-section-header {{ $operationalActive ? 'is-active' : '' }}"
                :aria-expanded="sectionStates['{{ $operationalSectionKey }}']"
                title="{{ $operationalSectionLabel }}"
            >
                <span class="flex items-center gap-2.5">
                    <x-nav-icon name="clipboard" class="h-4 w-4" />
                    <span class="sidebar-text">{{ $operationalSectionLabel }}</span>
                </span>
                <svg class="sidebar-section-chevron" :class="sectionStates['{{ $operationalSectionKey }}'] ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="sectionStates['{{ $operationalSectionKey }}']" x-cloak class="sidebar-section-content">
                <a href="{{ route('admin.events.index') }}" @click="sidebarOpen = false" title="Event" class="sidebar-link {{ $linkActive(request()->routeIs('admin.events.*')) }}">
                    <x-nav-icon name="calendar" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Event</span>
                </a>
                <a href="{{ route('admin.matches.index') }}" @click="sidebarOpen = false" title="Pertandingan" class="sidebar-link {{ $linkActive(request()->routeIs('admin.matches.*')) }}">
                    <x-nav-icon name="trophy" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Pertandingan</span>
                </a>
                <a href="{{ route('admin.verification.index') }}" @click="sidebarOpen = false" title="Verifikasi Pemain" class="sidebar-link {{ $linkActive(request()->routeIs('admin.verification.*')) }}">
                    <x-nav-icon name="user-check" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Verifikasi Pemain</span>
                </a>
                <a href="{{ route('admin.officials.verification.index') }}" @click="sidebarOpen = false" title="Verifikasi Official" class="sidebar-link {{ $linkActive(request()->routeIs('admin.officials.verification.*')) }}">
                    <x-nav-icon name="check-circle" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Verifikasi Official</span>
                </a>
                <a href="{{ route('admin.club-completeness.index') }}" @click="sidebarOpen = false" title="Kelengkapan Klub" class="sidebar-link {{ $linkActive(request()->routeIs('admin.club-completeness.*')) }}">
                    <x-nav-icon name="clipboard-check" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Kelengkapan Klub</span>
                </a>
            </div>
        @endif

        @if ($role === 'club-admin')
            <button
                type="button"
                @click="{!! $sectionToggle('menu-klub') !!}"
                class="sidebar-section-header {{ $clubMenuActive ? 'is-active' : '' }}"
                :aria-expanded="sectionStates['menu-klub']"
                title="Menu Klub"
            >
                <span class="flex items-center gap-2.5">
                    <x-nav-icon name="users" class="h-4 w-4" />
                    <span class="sidebar-text">Menu Klub</span>
                </span>
                <svg class="sidebar-section-chevron" :class="sectionStates['menu-klub'] ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="sectionStates['menu-klub']" x-cloak class="sidebar-section-content">
                <a href="{{ route('club-admin.players.index') }}" @click="sidebarOpen = false" title="Pemain" class="sidebar-link {{ $linkActive(request()->routeIs('club-admin.players.*')) }}">
                    <x-nav-icon name="user" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Pemain</span>
                </a>
                <a href="{{ route('club-admin.officials.index') }}" @click="sidebarOpen = false" title="Official Tim" class="sidebar-link {{ $linkActive(request()->routeIs('club-admin.officials.*') || request()->routeIs('club-admin.club.colors.*')) }}">
                    <x-nav-icon name="id-card" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Official Tim</span>
                </a>
                <a href="{{ route('club-admin.events.index') }}" @click="sidebarOpen = false" title="Daftar Event" class="sidebar-link {{ $linkActive(request()->routeIs('club-admin.events.*')) }}">
                    <x-nav-icon name="calendar" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Daftar Event</span>
                </a>
                <a href="{{ route('club-admin.lineups.index') }}" @click="sidebarOpen = false" title="Susunan Pemain" class="sidebar-link {{ $linkActive(request()->routeIs('club-admin.lineups.*')) }}">
                    <x-nav-icon name="list" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Susunan Pemain</span>
                </a>
                <a href="{{ route('club-admin.transfers.index') }}" @click="sidebarOpen = false" title="Mutasi" class="sidebar-link {{ $linkActive(request()->routeIs('club-admin.transfers.*')) }}">
                    <x-nav-icon name="swap" class="h-5 w-5 opacity-90" />
                    <span class="sidebar-text">Mutasi</span>
                </a>
            </div>
        @endif
    </nav>

    <div class="shrink-0 border-t border-blue-500/40 p-2 lg:p-3">
        @if (session('impersonator_id'))
            <div class="mb-2 rounded-lg bg-yellow-50 p-2.5 text-xs text-yellow-800">
                <span class="sidebar-text">Anda masuk sebagai <strong>{{ auth()->user()->name }}</strong>.</span>
                <form method="POST" action="{{ route('super-admin.impersonate.stop') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-1.5 font-semibold underline" title="Kembali ke akun asli">
                        <x-nav-icon name="logout" class="h-4 w-4" />
                        <span class="sidebar-text">Kembali ke akun asli</span>
                    </button>
                </form>
            </div>
        @endif
        <a
            href="{{ route('profile.edit') }}"
            @click="sidebarOpen = false"
            title="Profil"
            class="sidebar-link {{ $linkActive(request()->routeIs('profile.edit')) }}"
        >
            <x-nav-icon name="user" class="h-5 w-5 opacity-90" />
            <span class="sidebar-text">Profil</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit" title="Keluar" class="sidebar-link w-full bg-red-600 font-semibold text-white hover:bg-red-700">
                <x-nav-icon name="logout" class="h-5 w-5" />
                <span class="sidebar-text">Keluar</span>
            </button>
        </form>
    </div>
</aside>
