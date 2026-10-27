@php
    $activeEvent = $selectedEventId
        ? $availableEvents->firstWhere('id', $selectedEventId)
        : $featuredEvent;
    $nextMatch = $upcomingMatches->first();
    $fixtureList = $nextMatch ? $upcomingMatches->slice(1) : $upcomingMatches;
    $formPillClass = fn (string $outcome): string => match ($outcome) {
        'M' => 'home-form-win',
        'S' => 'home-form-draw',
        default => 'home-form-loss',
    };
    $dayNames = ['Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab', 'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab', 'Sun' => 'Min'];
    $monthNames = ['Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Agu', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'];
    $idDay = fn ($date): string => $dayNames[$date->format('D')] ?? $date->format('D');
    $idMonth = fn ($date): string => $monthNames[$date->format('M')] ?? $date->format('M');
    $idFullDate = fn ($date): string => $idDay($date).', '.$date->format('d').' '.$idMonth($date).' '.$date->format('Y');
@endphp

<x-public-layout title="SAPHYR - Beranda" active-section="beranda">
    <section class="home-hero relative" style="background-image: url('{{ asset('homehero.jpg') }}');">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-slate-900/80 via-slate-900/60 to-slate-900/80"></div>

        <div class="relative mx-auto max-w-7xl px-4 lg:px-8 py-20 lg:py-28">
            <div class="flex justify-center">
                <span class="home-hero-chip">ABEL GENTO</span>
            </div>
            <p class="mt-3 text-center text-xs font-bold uppercase tracking-[0.25em] text-blue-100">Platform Event Sepak Bola</p>
            <h1 class="home-hero-title mt-3">SAPHYR.</h1>
            <p class="mx-auto mt-4 max-w-2xl text-center text-sm leading-relaxed text-blue-100 md:text-base">
                Semua kompetisi, klub, jadwal, hasil, dan klasemen dalam satu tempat.
            </p>

            <form method="GET" action="{{ route('home') }}" class="home-search-bar">
                <div class="home-search-input-wrap flex-1 border-b border-slate-100 sm:border-b-0 sm:border-r">
                    <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input type="text" name="club" value="{{ $clubQuery }}" class="home-search-input" placeholder="Cari nama tim, kode klub...">
                </div>
                <div class="flex items-center gap-2 px-2">
                    <select name="event_id" class="home-search-input max-w-[220px] rounded-lg border border-slate-200 px-2 py-1.5 text-sm text-slate-700">
                        <option value="">Semua Event</option>
                        @foreach ($availableEvents as $event)
                            <option value="{{ $event->id }}" @selected((string) $selectedEventId === (string) $event->id)>{{ $event->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="home-btn-primary !px-5 !py-2.5 !text-xs">Cari</button>
                </div>
            </form>

            <div class="home-hero-stats !max-w-4xl !grid-cols-2 sm:!grid-cols-4">
                <div class="home-hero-stat">
                    <p class="home-hero-stat-value">{{ $totalEvents }}</p>
                    <p class="home-hero-stat-label">Event</p>
                </div>
                <div class="home-hero-stat">
                    <p class="home-hero-stat-value">{{ $totalClubs }}</p>
                    <p class="home-hero-stat-label">Klub</p>
                </div>
                <div class="home-hero-stat">
                    <p class="home-hero-stat-value">{{ $totalPlayers }}</p>
                    <p class="home-hero-stat-label">Pemain</p>
                </div>
                <div class="home-hero-stat">
                    <p class="home-hero-stat-value">{{ $totalMatches }}</p>
                    <p class="home-hero-stat-label">Pertandingan</p>
                </div>
            </div>
        </div>

        <svg class="home-hero-wave" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
            <path fill="currentColor" d="M0,48 C360,96 720,0 1080,48 C1260,72 1380,64 1440,56 L1440,80 L0,80 Z"></path>
        </svg>
    </section>

    <main class="pb-16">
        @if ($activeEvent || $clubQuery !== '')
            <div class="home-section !pb-0">
                <div class="home-context-bar">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-red-700">Filter Aktif</p>
                        <p class="mt-1 text-sm text-slate-700">
                            @if ($activeEvent)
                                Menampilkan data event <span class="font-semibold text-slate-900">{{ $activeEvent->name }}</span>
                            @endif
                            @if ($activeEvent && $clubQuery !== '')
                                ·
                            @endif
                            @if ($clubQuery !== '')
                                Pencarian tim: <span class="font-semibold text-slate-900">"{{ $clubQuery }}"</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('home') }}" class="home-btn-primary !bg-slate-900 !px-4 !py-2 !text-xs hover:!bg-slate-800">Reset Filter</a>
                </div>
            </div>
        @endif

        <section id="klasemen" class="home-section !pt-8">
            <x-public.section-heading
                eyebrow="Liga & Kompetisi"
                title="Klasemen"
                :subtitle="$activeEvent ? 'Peringkat '.$activeEvent->name.' berdasarkan poin, selisih gol, dan jumlah gol.' : 'Pilih event di bawah untuk melihat peringkat klub.'"
            >
                <x-slot name="actions">
                    <a href="{{ route('public.standings.index', $selectedEventId ? ['event_id' => $selectedEventId] : []) }}" class="home-btn-primary !bg-slate-900 !px-4 !py-2 !text-xs hover:!bg-slate-800">
                        Klasemen Lengkap
                    </a>
                </x-slot>
            </x-public.section-heading>

            @if ($availableEvents->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($availableEvents as $event)
                        <a
                            href="{{ route('home', array_filter(['event_id' => $event->id, 'club' => $clubQuery])) }}"
                            class="home-filter-pill {{ (string) ($activeEvent?->id) === (string) $event->id ? 'home-filter-pill-active' : '' }}"
                        >{{ $event->name }}</a>
                    @endforeach
                </div>
            @endif

            <div class="home-data-card-flush mt-5">
                @if ($standings->isNotEmpty())
                    <div class="home-table-wrap">
                        <table class="home-table">
                            <thead>
                                <tr>
                                    <th class="home-table-pos">#</th>
                                    <th class="text-left">Tim</th>
                                    <th class="text-center">Form</th>
                                    <th class="text-center">Main</th>
                                    <th class="text-center">M</th>
                                    <th class="text-center">S</th>
                                    <th class="text-center">K</th>
                                    <th class="text-center">GF</th>
                                    <th class="text-center">GA</th>
                                    <th class="text-center">SG</th>
                                    <th class="home-table-pts">Pts</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($standings as $idx => $row)
                                    @php
                                        $gd = $row['gf'] - $row['ga'];
                                        $guide = $formGuide[$row['club_id']] ?? [];
                                    @endphp
                                    <tr>
                                        <td class="home-table-pos">{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <x-public.club-badge :name="$row['club_name'] ?? null" :code="$row['club_code'] ?? null" :color="$row['shirt_color'] ?? null" />
                                                <span class="text-sm font-semibold text-slate-900">{{ $row['club_name'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center justify-center gap-1">
                                                @forelse (array_slice($guide, 0, 5) as $outcome)
                                                    <span class="home-form-pill {{ $formPillClass($outcome) }}" title="M: Menang · S: Seri · K: Kalah">{{ $outcome }}</span>
                                                @empty
                                                    <span class="text-xs text-slate-300">—</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="home-table-num">{{ $row['played'] ?? 0 }}</td>
                                        <td class="home-table-num">{{ $row['win'] ?? 0 }}</td>
                                        <td class="home-table-num">{{ $row['draw'] ?? 0 }}</td>
                                        <td class="home-table-num">{{ $row['loss'] ?? 0 }}</td>
                                        <td class="home-table-num">{{ $row['gf'] ?? 0 }}</td>
                                        <td class="home-table-num">{{ $row['ga'] ?? 0 }}</td>
                                        <td class="home-table-num {{ $gd > 0 ? 'text-emerald-600' : ($gd < 0 ? 'text-red-600' : '') }}">{{ $gd > 0 ? '+'.$gd : $gd }}</td>
                                        <td class="home-table-pts">{{ $row['points'] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="border-t border-slate-100 px-5 py-3 text-[11px] text-slate-400">
                        M: menang · S: seri · K: kalah · GF: gol untuk · GA: gol kebobolan · SG: selisih gol
                    </p>
                @else
                    <div class="rounded-b-2xl border-t border-dashed border-slate-200 p-10 text-center">
                        <p class="text-sm text-slate-500">Belum ada pertandingan yang selesai, jadi klasemen masih kosong.</p>
                    </div>
                @endif
            </div>
        </section>

        <section id="jadwal" class="home-section home-section-alt !pt-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="home-data-card-flush h-full">
                        <x-public.section-heading
                            eyebrow="Jadwal"
                            title="Pertandingan Mendatang"
                            :subtitle="$activeEvent ? $activeEvent->name : 'Jadwal terdekat yang belum dimulai.'"
                            class="!px-5 !pt-5"
                        />

                        @if ($nextMatch)
                            <div class="mx-5 mt-4 flex flex-col items-center gap-3 rounded-2xl bg-[linear-gradient(120deg,#0b1b3b_0%,#123f8f_55%,#c0182f_100%)] px-5 py-5 text-white sm:flex-row sm:justify-between">
                                <div class="text-center sm:text-left">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-blue-100">Matchday Berikutnya</p>
                                    <p class="mt-1 text-lg font-extrabold uppercase leading-tight">
                                        {{ $nextMatch->homeClub?->name }} <span class="text-white/60">vs</span> {{ $nextMatch->awayClub?->name }}
                                    </p>
                                    <p class="mt-1 text-xs text-blue-100">
                                        {{ $idFullDate($nextMatch->match_date) }} · {{ $nextMatch->match_date->format('H:i') }} WIB · {{ $nextMatch->stadium?->name ?? 'Stadion belum ditentukan' }}
                                    </p>
                                </div>
                                <span class="home-score-box home-score-box-lg">{{ $nextMatch->match_date->format('H:i') }}</span>
                            </div>
                        @endif

                        <div class="home-list">
                            @forelse ($fixtureList as $match)
                                <div class="home-fixture-row">
                                    <div class="home-fixture-date">
                                        <span class="home-fixture-day">{{ $match->match_date->format('d') }}</span>
                                        <span class="home-fixture-month">{{ $idDay($match->match_date) }}</span>
                                    </div>
                                    <div class="home-fixture-teams">
                                        <p>{{ $match->homeClub?->name ?? 'Tim tuan rumah' }} <span class="text-slate-400">vs</span> {{ $match->awayClub?->name ?? 'Tim tamu' }}</p>
                                        <p class="home-fixture-meta">{{ $match->match_date->format('H:i') }} WIB · {{ $match->stadium?->name ?? 'Stadion belum ditentukan' }}</p>
                                    </div>
                                    <a href="{{ route('public.events.show', $match->event_id) }}" class="home-filter-pill shrink-0">Detail</a>
                                </div>
                            @empty
                                <div class="p-10 text-center">
                                    <p class="text-sm text-slate-500">Belum ada jadwal pertandingan mendatang.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div>
                    <div class="home-data-card-flush h-full">
                        <x-public.section-heading
                            eyebrow="Golden Boot"
                            title="Top Skor"
                            :subtitle="$activeEvent ? $activeEvent->name : 'Pemain dengan gol terbanyak.'"
                            class="!px-5 !pt-5"
                        />
                        <div class="home-list">
                            @forelse ($topScorers as $idx => $scorer)
                                <div class="home-scorer-row">
                                    <span class="home-rank-badge {{ $idx === 0 ? 'home-rank-gold' : ($idx === 1 ? 'home-rank-silver' : ($idx === 2 ? 'home-rank-bronze' : '')) }}">{{ $idx + 1 }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ $scorer->name }}</p>
                                        <p class="truncate text-xs text-slate-500">Pemain</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-base font-extrabold tabular-nums text-slate-900">{{ $scorer->total_goals }}</p>
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Gol</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 text-center">
                                    <p class="text-sm text-slate-500">Belum ada gol yang tercatat.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-section !pt-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div id="hasil" class="home-data-card-flush h-full">
                        <x-public.section-heading
                            eyebrow="Hasil"
                            title="Hasil Terakhir"
                            subtitle="Pertandingan yang sudah punya hasil lengkap beserta gol pencetaknya."
                            class="!px-5 !pt-5"
                        />
                        <div class="home-list">
                            @forelse ($recentResults as $match)
                                <div class="home-fixture-row">
                                    <div class="home-fixture-date">
                                        <span class="home-fixture-day">{{ $match->match_date->format('d') }}</span>
                                        <span class="home-fixture-month">{{ $idMonth($match->match_date) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm font-semibold text-slate-800">
                                            <x-public.club-badge :club="$match->homeClub" size="sm" />
                                            <span class="truncate">{{ $match->homeClub?->name }}</span>
                                            <span class="home-score-box">
                                                {{ $match->result?->home_score ?? 0 }}
                                                <span class="text-slate-400">-</span>
                                                {{ $match->result?->away_score ?? 0 }}
                                            </span>
                                            <x-public.club-badge :club="$match->awayClub" size="sm" />
                                            <span class="truncate">{{ $match->awayClub?->name }}</span>
                                            <span class="home-filter-pill !px-2 !py-0.5 !text-[10px]">FT</span>
                                        </div>
                                        @include('partials.match-goal-scorers', ['match' => $match])
                                    </div>
                                    <div class="home-fixture-meta md:text-right">
                                        <p>{{ $match->match_date->format('H:i') }} WIB</p>
                                        <p class="mt-1">{{ $match->stadium?->name ?? '-' }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 text-center">
                                    <p class="text-sm text-slate-500">Belum ada hasil pertandingan.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div x-data="{ eventFilter: 'semua' }">
                    <x-public.section-heading
                        eyebrow="Kompetisi"
                        title="Event"
                        subtitle="Filter event berdasarkan status dan jenis kompetisi."
                    >
                        <x-slot name="actions">
                            <button type="button" @click="eventFilter = 'semua'" :class="eventFilter === 'semua' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Semua</button>
                            <button type="button" @click="eventFilter = 'berlangsung'" :class="eventFilter === 'berlangsung' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Berlangsung</button>
                            <button type="button" @click="eventFilter = 'liga'" :class="eventFilter === 'liga' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Liga</button>
                            <button type="button" @click="eventFilter = 'knockout'" :class="eventFilter === 'knockout' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Knockout</button>
                        </x-slot>
                    </x-public.section-heading>

                    <div class="mt-4 space-y-3">
                        @forelse ($availableEvents->take(5) as $event)
                            @php
                                $isFinished = $event->end_date && $event->end_date->isPast();
                                $isUpcoming = $event->start_date && $event->start_date->isFuture();
                                $statusLabel = $isFinished ? 'Selesai' : ($isUpcoming ? 'Akan Datang' : 'Berlangsung');
                                $statusClass = $isFinished ? 'bg-slate-600' : ($isUpcoming ? 'bg-amber-500' : 'bg-emerald-500');
                                $filterKey = $isFinished ? 'selesai' : 'berlangsung';
                            @endphp
                            <article
                                class="home-event-card !flex-row items-center gap-4 !p-4 {{ (string) ($activeEvent?->id) === (string) $event->id ? 'ring-2 ring-red-500 ring-offset-2' : '' }}"
                                x-show="eventFilter === 'semua' || eventFilter === '{{ $filterKey }}' || eventFilter === '{{ strtolower($event->competition_type) }}'"
                                x-cloak
                            >
                                <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-[linear-gradient(140deg,#1145a8_0%,#c61f3a_100%)] text-white">
                                    <span class="text-lg font-extrabold leading-none">{{ $event->start_date?->format('d') ?? '?' }}</span>
                                    <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-blue-100">{{ $event->start_date ? $idMonth($event->start_date) : '' }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-900">{{ $event->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ strtoupper($event->competition_type) }} · U-{{ $event->min_age ?? '?' }} s/d U-{{ $event->max_age ?? '?' }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-400">{{ $event->start_date?->format('d') ?? '-' }} {{ $event->start_date ? $idMonth($event->start_date) : '' }} {{ $event->start_date?->format('Y') ?? '' }} – {{ $event->end_date?->format('d') ?? '-' }} {{ $event->end_date ? $idMonth($event->end_date) : '' }} {{ $event->end_date?->format('Y') ?? '' }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="inline-block rounded-full {{ $statusClass }} px-2.5 py-0.5 text-[10px] font-bold uppercase text-white">{{ $statusLabel }}</span>
                                    <a href="{{ route('public.events.show', $event->id) }}" class="mt-2 block text-xs font-bold uppercase tracking-wide text-red-600 hover:text-red-800">Detail</a>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                                <p class="text-sm text-slate-500">Belum ada event.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 flex justify-center">
                        <a href="{{ route('public.events.index') }}" class="home-btn-primary !bg-slate-900 !px-4 !py-2 !text-xs hover:!bg-slate-800">Lihat Semua Event</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="tim" class="home-section home-section-alt !pt-8">
            <x-public.section-heading
                eyebrow="Klub"
                title="Profil Tim"
                subtitle="Daftar klub aktif beserta jumlah pemain dan petugas yang terdaftar."
            >
                <x-slot name="actions">
                    <a href="{{ route('public.teams.index') }}" class="home-btn-primary !bg-slate-900 !px-4 !py-2 !text-xs hover:!bg-slate-800">Lihat Semua Tim</a>
                </x-slot>
            </x-public.section-heading>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @forelse ($clubs->take(8) as $club)
                    <article class="home-club-card">
                        <x-public.club-badge :club="$club" size="lg" />
                        <h3 class="mt-3 text-sm font-bold text-slate-900">{{ $club->name }}</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $club->players_count }} pemain · {{ $club->officials_count }} petugas
                        </p>
                        <div class="mt-3 flex w-full flex-wrap items-center justify-center gap-1.5">
                            @foreach (['shirt_color' => 'Baju', 'pants_color' => 'Celana', 'socks_color' => 'Kaus'] as $field => $label)
                                @if ($club->{$field})
                                    <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                        <span class="h-2.5 w-2.5 rounded-full border border-slate-300" style="background-color: {{ $club->{$field} }};"></span>
                                        {{ $label }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                        <a href="{{ route('home', array_filter(['club' => $club->name, 'event_id' => $selectedEventId])) }}#jadwal" class="mt-4 text-xs font-bold uppercase tracking-wide text-red-600 hover:text-red-800">Lihat Jadwal Tim</a>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                        <p class="text-sm text-slate-500">Belum ada tim terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="home-section !pt-8">
            <div class="home-cta-banner">
                <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-red-500/20 blur-3xl"></div>
                <div class="relative grid gap-6 md:grid-cols-2 md:items-center">
                    <div>
                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">Live Update</span>
                        <h2 class="mt-4 text-2xl font-extrabold uppercase tracking-tight md:text-3xl">
                            Pantau Event Secara Real-Time
                        </h2>
                        <p class="mt-3 text-sm leading-relaxed text-blue-100">
                            Jadwal, hasil skor, dan klasemen diperbarui otomatis setelah setiap pertandingan selesai.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3 md:justify-end">
                        <a href="#jadwal" class="home-btn-outline">Jadwal</a>
                        <a href="#hasil" class="home-btn-primary !bg-white !text-red-700 hover:!bg-red-50">Hasil Terbaru</a>
                        <a href="{{ route('public.standings.index') }}" class="home-btn-outline">Klasemen</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-public-layout>
