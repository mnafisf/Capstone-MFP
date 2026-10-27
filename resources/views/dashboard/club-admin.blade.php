@php
    $playerApprovalPct = $stats['players_total'] > 0
        ? (int) round(($stats['players_approved'] / $stats['players_total']) * 100)
        : 0;
    $officialApprovalPct = $stats['officials_total'] > 0
        ? (int) round(($stats['officials_approved'] / $stats['officials_total']) * 100)
        : 0;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Admin Klub</span>
                    <h2 class="mt-3 text-3xl font-bold leading-tight lg:text-4xl">{{ $club->name }}</h2>
                    @if ($club->code)
                        <p class="mt-1 text-sm font-medium text-blue-100">Kode klub: {{ $club->code }}</p>
                    @endif
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-blue-100">
                        Halo <span class="font-semibold text-white">{{ auth()->user()->name }}</span>,
                        pantau status tim dan selesaikan tugas administrasi kompetisi dari satu tempat.
                    </p>
                </div>
                <div class="super-stat-grid lg:max-w-md">
                    <div class="super-stat-card-strong">
                        <p class="super-stat-label">Pemain Aktif</p>
                        <p class="super-stat-value">{{ $stats['players_approved'] }}</p>
                    </div>
                    <div class="super-stat-card-strong">
                        <p class="super-stat-label">Event Terdaftar</p>
                        <p class="super-stat-value">{{ $stats['event_registrations'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Pemain</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['players_total'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-red-500 to-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
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
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['officials_total'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-blue-500 to-blue-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.09 9.09 0 01-5.656 2.105 9.09 9.09 0 01-5.656-2.105M15 9.75a3 3 0 11-6 0 3 3 0 016 0zm6 2.25a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                    </svg>
                </div>
            </div>
            <div class="dash-progress-track">
                <div class="dash-progress-bar" style="width: {{ $officialApprovalPct }}%"></div>
            </div>
            <p class="mt-2 text-xs text-slate-500">{{ $officialApprovalPct }}% disetujui · {{ $stats['officials_pending'] }} pending</p>
        </div>

        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pendaftaran Event</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['event_registrations'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 8.25h15m-13.5 0V19.5A2.25 2.25 0 006.75 21.75h10.5A2.25 2.25 0 0019.5 19.5V8.25m-15 0h15M9 12.75h.008v.008H9v-.008zm3 0h.008v.008H12v-.008zm3 0h.008v.008H15v-.008z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">Pemain terdaftar ke kompetisi aktif</p>
        </div>

        <div class="dash-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Operasional</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $stats['lineups'] }}</p>
                </div>
                <div class="dash-stat-icon bg-gradient-to-br from-violet-500 to-violet-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 8.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ $stats['lineups'] }} susunan · {{ $stats['active_transfers'] }} mutasi aktif</p>
        </div>
    </div>

    @if (! $canManageLineups)
        <div class="mt-6 flex gap-3 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 px-5 py-4 shadow-sm">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
            <div class="text-sm text-amber-900">
                <p class="font-semibold">Perhatian batas waktu kompetisi</p>
                <ul class="mt-1 space-y-1 text-amber-800">
                    @if (! $canManageLineups)
                        <li>Pengaturan susunan pemain ditutup{{ $lineupDeadline ? ' · '.$lineupDeadline->format('d M Y H:i') : '' }}.</li>
                    @endif
                </ul>
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="surface-red-blue lg:col-span-2">
            <div class="relative z-10">
                <span class="chip-soft border-white/40 bg-white/60 text-slate-700">Pusat Kontrol</span>
                <h3 class="mt-3 text-xl font-bold text-slate-900">Tugas Admin Klub</h3>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                    Kelola seluruh data <span class="font-semibold text-blue-700">{{ $club->name }}</span> mulai dari registrasi pemain,
                    official tim, hingga persiapan pertandingan. Setiap pengajuan akan diverifikasi Admin Panitia.
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('club-admin.players.index') }}" class="dash-action-card">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Manajemen Pemain</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Input data, dokumen, dan pantau status verifikasi pemain.</p>
                                @if ($stats['players_pending'] > 0)
                                    <span class="status-badge status-warning mt-2">{{ $stats['players_pending'] }} pending</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('club-admin.officials.index') }}" class="dash-action-card">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.09 9.09 0 01-5.656 2.105 9.09 9.09 0 01-5.656-2.105M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Official Tim</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Ajukan pelatih, asisten, manager, dan staff medis.</p>
                                @if ($stats['officials_pending'] > 0)
                                    <span class="status-badge status-warning mt-2">{{ $stats['officials_pending'] }} pending</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('club-admin.events.index') }}" class="dash-action-card">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 8.25h15m-13.5 0V19.5A2.25 2.25 0 006.75 21.75h10.5A2.25 2.25 0 0019.5 19.5V8.25"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Daftar Event</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Daftarkan pemain ke event sesuai kategori umur.</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('club-admin.lineups.index') }}" class="dash-action-card">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.123.433-.177.664m19.5 0c-.065-.21-.123-.433-.177-.664M6.75 6.75h.008v.008H6.75V6.75zm0 3h.008v.008H6.75V9.75zm0 3h.008v.008H6.75v-.008z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Susunan Pemain</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Atur starting XI, cadangan, dan unduh DSP.</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('club-admin.transfers.index') }}" class="dash-action-card sm:col-span-2">
                        <div class="relative z-10 flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-slate-900">Mutasi Pemain</p>
                                    <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600">→</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Kelola pelepasan dan penerimaan pemain antar klub.</p>
                                @if ($stats['active_transfers'] > 0)
                                    <span class="status-badge status-warning mt-2">{{ $stats['active_transfers'] }} mutasi berjalan</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card overflow-hidden p-0">
                <div class="bg-gradient-to-r from-blue-600 to-red-500 px-5 py-4">
                    <h3 class="text-base font-bold text-white">Status Verifikasi</h3>
                    <p class="text-xs text-blue-100">Ringkasan persetujuan panitia</p>
                </div>
                <div class="space-y-2 p-4">
                    <div class="flex items-center gap-3 rounded-xl bg-emerald-50 px-3 py-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500 text-sm font-bold text-white">{{ $stats['players_approved'] }}</div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-700">Pemain disetujui</p>
                            <p class="text-xs text-slate-500">Siap didaftarkan ke event</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-amber-50 px-3 py-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500 text-sm font-bold text-white">{{ $stats['players_pending'] }}</div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-700">Pemain pending</p>
                            <p class="text-xs text-slate-500">Menunggu review panitia</p>
                        </div>
                    </div>
                    @if ($stats['players_rejected'] > 0)
                        <div class="flex items-center gap-3 rounded-xl bg-rose-50 px-3 py-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-500 text-sm font-bold text-white">{{ $stats['players_rejected'] }}</div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-700">Pemain ditolak</p>
                                <p class="text-xs text-slate-500">Perlu perbaikan data</p>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-center gap-3 rounded-xl bg-blue-50 px-3 py-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-sm font-bold text-white">{{ $stats['officials_approved'] }}</div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-700">Official disetujui</p>
                            <p class="text-xs text-slate-500">Staff tim terverifikasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Jadwal Mendatang</h3>
                        <p class="text-xs text-slate-500">Pertandingan klub Anda</p>
                    </div>
                    <span class="chip-soft">{{ $upcomingMatches->count() }} match</span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($upcomingMatches as $match)
                        <div class="dash-match-ticket pl-4">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">{{ $match->event->name }}</p>
                            <p class="mt-1 text-sm font-bold text-slate-800">
                                {{ $match->homeClub->name }}
                                <span class="mx-1 font-normal text-slate-400">vs</span>
                                {{ $match->awayClub->name }}
                            </p>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                                <span class="inline-flex items-center gap-1 rounded-full bg-white px-2 py-0.5 border border-slate-200">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $match->match_date->format('d M Y, H:i') }}
                                </span>
                                @if ($match->stadium)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white px-2 py-0.5 border border-slate-200">
                                        {{ $match->stadium->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-slate-600">Belum ada jadwal</p>
                            <p class="mt-1 text-xs text-slate-500">Pertandingan mendatang akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>

                @if ($upcomingMatches->isNotEmpty())
                    <a href="{{ route('club-admin.lineups.index') }}" class="btn-primary mt-4 w-full justify-center">
                        Atur Susunan Pemain
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
