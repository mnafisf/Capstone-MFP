<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                    <h2 class="mt-3 text-3xl font-bold leading-tight lg:text-4xl">Dashboard Super Admin</h2>
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-blue-100">
                        Halo <span class="font-semibold text-white">{{ auth()->user()->name }}</span>,
                        kelola seluruh sistem kompetisi — pengguna, infrastruktur, dan operasional panitia.
                    </p>
                </div>
                <div class="super-stat-grid lg:max-w-md">
                    <div class="super-stat-card-strong">
                        <p class="super-stat-label">Klub Aktif</p>
                        <p class="super-stat-value">{{ $systemStats['clubs_active'] }}</p>
                    </div>
                    <div class="super-stat-card-strong">
                        <p class="super-stat-label">Menunggu Verifikasi</p>
                        <p class="super-stat-value">{{ $stats['players_pending'] + $stats['officials_pending'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Admin Panitia</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $systemStats['admins'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-indigo-500 to-indigo-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ $systemStats['club_admins'] }} admin klub terdaftar</p>
        </div>

        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Stadion</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $systemStats['stadiums'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008H17.25v-.008z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ $systemStats['referees'] }} wasit terdaftar</p>
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
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kompetisi</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['events_total'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-blue-500 to-blue-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 8.25h15m-13.5 0V19.5A2.25 2.25 0 006.75 21.75h10.5A2.25 2.25 0 0019.5 19.5V8.25"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ $stats['matches_total'] }} pertandingan · {{ $stats['clubs_total'] }} klub</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="surface-red-blue">
                <div class="relative z-10">
                    <span class="chip-soft border-white/40 bg-white/60 text-slate-700">Manajemen Sistem</span>
                    <h3 class="mt-3 text-xl font-bold text-slate-900">Pengaturan Platform</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                        Kelola akun admin panitia, admin klub, stadion, wasit, dan batas waktu kompetisi.
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <a href="{{ route('super-admin.admins.index') }}" class="dash-action-card group">
                            <div class="relative z-10 flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-slate-900">Admin Panitia</p>
                                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">Buat dan kelola akun admin panitia kompetisi.</p>
                                    <span class="status-badge status-active mt-2">{{ $systemStats['admins'] }} akun</span>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('super-admin.club-admins.index') }}" class="dash-action-card group">
                            <div class="relative z-10 flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.09 9.09 0 01-5.656 2.105 9.09 9.09 0 01-5.656-2.105M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-slate-900">Admin Klub</p>
                                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">Kelola akun admin untuk setiap klub peserta.</p>
                                    <span class="status-badge status-active mt-2">{{ $systemStats['club_admins'] }} akun</span>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('super-admin.stadiums.index') }}" class="dash-action-card group">
                            <div class="relative z-10 flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008H17.25v-.008z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-slate-900">Stadion</p>
                                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">Data venue pertandingan kompetisi.</p>
                                    <span class="status-badge status-active mt-2">{{ $systemStats['stadiums'] }} stadion</span>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('super-admin.referees.index') }}" class="dash-action-card group">
                            <div class="relative z-10 flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-slate-900">Wasit</p>
                                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">Kelola data wasit pertandingan.</p>
                                    <span class="status-badge status-active mt-2">{{ $systemStats['referees'] }} wasit</span>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('super-admin.settings.index') }}" class="dash-action-card group sm:col-span-2">
                            <div class="relative z-10 flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-slate-900">Pengaturan Lineup Pertandingan</p>
                                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">Atur batas input lineup pertandingan klub.</p>
                                    @if ($lineupDeadline)
                                        <span class="status-badge status-warning mt-2">Deadline aktif</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <span class="chip-soft">Operasional</span>
                <h3 class="mt-3 text-xl font-bold text-slate-900">Modul Admin Panitia</h3>
                <p class="mt-2 text-sm text-slate-600">Akses cepat ke fitur operasional kompetisi yang sama dengan admin panitia.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <a href="{{ route('admin.events.index') }}" class="dash-action-card group">
                        <div class="relative z-10">
                            <p class="font-bold text-slate-900">Event</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $stats['events_total'] }} event</p>
                        </div>
                    </a>

                    <a href="{{ route('super-admin.transfers.verification.index') }}" class="dash-action-card group">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Verifikasi Mutasi</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600"></span>
                                </div>
                                    <span class="status-badge status-warning mt-2">{{ $systemStats['transfer_pending'] ?? 0 }} pending</span>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('admin.matches.index') }}" class="dash-action-card group">
                        <div class="relative z-10">
                            <p class="font-bold text-slate-900">Pertandingan</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $stats['matches_upcoming'] }} mendatang</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.verification.index') }}" class="dash-action-card group">
                        <div class="relative z-10">
                            <p class="font-bold text-slate-900">Verifikasi Pemain</p>
                            @if ($stats['players_pending'] > 0)
                                <span class="status-badge status-warning mt-2">{{ $stats['players_pending'] }} pending</span>
                            @else
                                <p class="mt-1 text-xs text-slate-500">Tidak ada pending</p>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('admin.officials.verification.index') }}" class="dash-action-card group">
                        <div class="relative z-10">
                            <p class="font-bold text-slate-900">Verifikasi Official</p>
                            @if ($stats['officials_pending'] > 0)
                                <span class="status-badge status-warning mt-2">{{ $stats['officials_pending'] }} pending</span>
                            @else
                                <p class="mt-1 text-xs text-slate-500">Tidak ada pending</p>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('admin.club-completeness.index') }}" class="dash-action-card group sm:col-span-2 lg:col-span-1">
                        <div class="relative z-10">
                            <p class="font-bold text-slate-900">Kelengkapan Klub</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $stats['clubs_total'] }} klub</p>
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
