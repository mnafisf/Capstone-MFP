<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Admin Panitia</span>
                    <h2 class="mt-3 text-3xl font-bold leading-tight lg:text-4xl">Dashboard Panitia</h2>
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-blue-100">
                        Halo <span class="font-semibold text-white">{{ auth()->user()->name }}</span>,
                        kelola kompetisi, verifikasi data klub, dan pantau jadwal pertandingan dari satu tempat.
                    </p>
                </div>
                <div class="super-stat-grid lg:max-w-md">
                    <div class="super-stat-card-strong">
                        <p class="super-stat-label">Menunggu Verifikasi</p>
                        <p class="super-stat-value">{{ $stats['players_pending'] + $stats['officials_pending'] }}</p>
                    </div>
                    <div class="super-stat-card-strong">
                        <p class="super-stat-label">Pertandingan Mendatang</p>
                        <p class="super-stat-value">{{ $stats['matches_upcoming'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Klub Terdaftar</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['clubs_total'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-blue-500 to-blue-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008H17.25v-.008zm0 3h.008v.008H17.25v-.008zm0 3h.008v.008H17.25v-.008z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">Total klub dalam sistem kompetisi</p>
        </div>

        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pemain</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['players_total'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-red-500 to-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/>
                    </svg>
                </div>
            </div>
            <div class="dash-progress-track">
                <div class="dash-progress-bar" style="width: {{ $playerApprovalPct }}%"></div>
            </div>
            <p class="mt-2 text-xs text-slate-500">{{ $playerApprovalPct }}% disetujui · {{ $stats['players_pending'] }} pending</p>
        </div>

        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Official Tim</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['officials_approved'] + $stats['officials_pending'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-violet-500 to-violet-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.09 9.09 0 01-5.656 2.105 9.09 9.09 0 01-5.656-2.105M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ $stats['officials_pending'] }} menunggu verifikasi</p>
        </div>

        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Event & Pertandingan</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['events_total'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 8.25h15m-13.5 0V19.5A2.25 2.25 0 006.75 21.75h10.5A2.25 2.25 0 0019.5 19.5V8.25"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ $stats['matches_total'] }} pertandingan · {{ $stats['registrations'] }} pendaftaran event</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="surface-red-blue lg:col-span-2">
            <div class="relative z-10">
                <span class="chip-soft border-white/40 bg-white/60 text-slate-700">Pusat Kontrol</span>
                <h3 class="mt-3 text-xl font-bold text-slate-900">Tugas Admin Panitia</h3>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                    Kelola event dan pertandingan, verifikasi data pemain serta official tim,
                    dan pantau kelengkapan administrasi setiap klub peserta kompetisi.
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('admin.events.index') }}" class="dash-action-card group">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 8.25h15m-13.5 0V19.5A2.25 2.25 0 006.75 21.75h10.5A2.25 2.25 0 0019.5 19.5V8.25"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Manajemen Event</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Buat dan kelola event kompetisi beserta kategori umur.</p>
                                <span class="status-badge status-active mt-2">{{ $stats['events_total'] }} event</span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.matches.index') }}" class="dash-action-card group">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Pertandingan</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Jadwal, skor, gol, dan ringkasan pertandingan.</p>
                                <span class="status-badge status-active mt-2">{{ $stats['matches_upcoming'] }} mendatang</span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.verification.index') }}" class="dash-action-card group">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Verifikasi Pemain</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Review dan setujui data pemain dari setiap klub.</p>
                                @if ($stats['players_pending'] > 0)
                                    <span class="status-badge status-warning mt-2">{{ $stats['players_pending'] }} pending</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.officials.verification.index') }}" class="dash-action-card group">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.09 9.09 0 01-5.656 2.105 9.09 9.09 0 01-5.656-2.105M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Verifikasi Official</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Setujui pelatih, asisten, manager, dan staff medis.</p>
                                @if ($stats['officials_pending'] > 0)
                                    <span class="status-badge status-warning mt-2">{{ $stats['officials_pending'] }} pending</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.club-completeness.index') }}" class="dash-action-card group sm:col-span-2">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.123.433-.177.664m19.5 0c-.065-.21-.123-.433-.177-.664M6.75 6.75h.008v.008H6.75V6.75z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Kelengkapan Klub</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Pantau status administrasi dan kelengkapan data tiap klub peserta.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if ($stats['players_pending'] > 0 || $stats['officials_pending'] > 0)
                <div class="card overflow-hidden p-0">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-5 py-4">
                        <h3 class="text-base font-bold text-white">Perlu Tindakan</h3>
                        <p class="text-xs text-amber-100">Verifikasi menunggu review</p>
                    </div>
                    <div class="space-y-2 p-4">
                        @if ($stats['players_pending'] > 0)
                            <a href="{{ route('admin.verification.index') }}" class="flex items-center gap-3 rounded-xl bg-amber-50 px-3 py-3 transition hover:bg-amber-100">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500 text-sm font-bold text-white">{{ $stats['players_pending'] }}</div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-700">Pemain pending</p>
                                    <p class="text-xs text-slate-500">Klik untuk verifikasi</p>
                                </div>
                            </a>
                        @endif
                        @if ($stats['officials_pending'] > 0)
                            <a href="{{ route('admin.officials.verification.index') }}" class="flex items-center gap-3 rounded-xl bg-violet-50 px-3 py-3 transition hover:bg-violet-100">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-500 text-sm font-bold text-white">{{ $stats['officials_pending'] }}</div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-700">Official pending</p>
                                    <p class="text-xs text-slate-500">Klik untuk verifikasi</p>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Pemain Pending</h3>
                        <p class="text-xs text-slate-500">Pengajuan terbaru</p>
                    </div>
                    <a href="{{ route('admin.verification.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse ($pendingPlayers as $player)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                            <p class="text-sm font-semibold text-slate-800">{{ $player->name }}</p>
                            <p class="text-xs text-slate-500">{{ $player->club?->name ?? 'Tanpa klub' }}</p>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Tidak ada pengajuan pending.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Jadwal Mendatang</h3>
                        <p class="text-xs text-slate-500">Pertandingan kompetisi</p>
                    </div>
                    <span class="chip-soft">{{ $upcomingMatches->count() }} match</span>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($upcomingMatches as $match)
                        <a href="{{ route('admin.matches.index', ['match_id' => $match->id]) }}" class="dash-match-ticket block pl-4 transition hover:border-blue-300">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">{{ $match->event->name }}</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">
                                {{ $match->homeClub->name }}
                                <span class="mx-1 font-normal text-slate-400">vs</span>
                                {{ $match->awayClub->name }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">{{ $match->match_date->format('d M Y, H:i') }}</p>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-slate-600">Belum ada jadwal</p>
                        </div>
                    @endforelse
                </div>
                @if ($upcomingMatches->isNotEmpty())
                    <a href="{{ route('admin.matches.index') }}" class="btn-primary mt-4 w-full justify-center">Kelola Pertandingan</a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
