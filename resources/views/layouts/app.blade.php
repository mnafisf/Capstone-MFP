<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sidebar="expanded">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SAPHYR') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <script>
            // Ditetapkan sebelum body dirender supaya tidak ada kedipan (FOUC)
            // dan tidak perlu menunggu Alpine.js selesai di-inisialisasi.
            (function () {
                try {
                    document.documentElement.dataset.sidebar = localStorage.getItem('sidebarDesktopOpen') === 'false'
                        ? 'collapsed'
                        : 'expanded';
                } catch (e) {
                    document.documentElement.dataset.sidebar = 'expanded';
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 antialiased" style="font-family: 'Barlow', sans-serif;">
        <div
            x-data="{
                sidebarOpen: false,
                desktopOpen: true,
                defaultSectionStates: {
                    'master-data': true,
                    'operasional-panitia': true,
                    'menu-utama': true,
                    'menu-klub': true,
                },
                sectionStates: {},
                init() {
                    this.desktopOpen = localStorage.getItem('sidebarDesktopOpen') !== 'false';

                    this.sectionStates = { ...this.defaultSectionStates };
                    const savedSections = localStorage.getItem('sidebarSectionStates');
                    if (savedSections !== null) {
                        try {
                            this.sectionStates = { ...this.sectionStates, ...JSON.parse(savedSections) };
                        } catch (e) {}
                    }

                    const aside = this.$el ? this.$el.querySelector('[data-active-section]') : null;
                    const activeSection = aside ? aside.dataset.activeSection : null;
                    if (activeSection && Object.prototype.hasOwnProperty.call(this.sectionStates, activeSection)) {
                        this.sectionStates[activeSection] = true;
                    }
                },
                syncSidebar() {
                    document.documentElement.dataset.sidebar = this.desktopOpen ? 'expanded' : 'collapsed';
                },
                toggleDesktopSidebar() {
                    this.desktopOpen = !this.desktopOpen;
                    localStorage.setItem('sidebarDesktopOpen', this.desktopOpen);
                    this.syncSidebar();
                },
                toggleSection(section) {
                    this.sectionStates[section] = !this.sectionStates[section];
                    localStorage.setItem('sidebarSectionStates', JSON.stringify(this.sectionStates));
                },
                openSection(section) {
                    if (!this.desktopOpen) {
                        this.desktopOpen = true;
                        localStorage.setItem('sidebarDesktopOpen', this.desktopOpen);
                        this.syncSidebar();
                    }
                    this.sectionStates[section] = true;
                    localStorage.setItem('sidebarSectionStates', JSON.stringify(this.sectionStates));
                },
            }"
            class="min-h-screen"
        >
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"></div>

            @include('layouts.navigation')

            <main class="app-main min-h-screen p-4 lg:p-8">
                <div class="mb-6 flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="toggleDesktopSidebar()"
                            class="sidebar-toggle-topbar h-10 w-10 items-center justify-center rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100"
                            aria-label="Tampilkan sidebar"
                            title="Tampilkan sidebar"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <button type="button" @click="sidebarOpen = !sidebarOpen; if (sidebarOpen) { desktopOpen = true; localStorage.setItem('sidebarDesktopOpen', true); syncSidebar(); }" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 lg:hidden" aria-label="Toggle sidebar">
                            <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="sidebarOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">SAPHYR</p>
                        <p class="text-sm font-semibold text-slate-800">Dasbor Kompetisi</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Pengguna Aktif</p>
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                    </div>
                </div>

                @isset($header)
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endisset

                @if (session('success'))
                    <div class="mb-6 alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 alert-error">
                        <ul class="list-disc ps-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
