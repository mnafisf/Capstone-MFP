@php
    $formattedCompetitionType = $event->competition_type === 'league' ? 'Liga' : 'Knockout';
    $isLeague = $event->competition_type === 'league';
    $isKnockout = $event->competition_type === 'knockout';
    $hasStandings = $isLeague && $standings->isNotEmpty();
    $upcomingMatches = $matches->filter(fn ($match) => ! $match->result);
    $finishedMatches = $matches->filter(fn ($match) => $match->result);
    $idMonth = fn ($date): string => ['Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Agu', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'][$date->format('M')] ?? $date->format('M');
@endphp

<x-public-layout title="SAPHYR - {{ $event->name }}" active-section="event">
    <main class="pb-16">
        <section class="home-section">
            <div class="home-cta-banner">
                <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
                <div class="relative grid gap-6 lg:grid-cols-[1.4fr_0.9fr] lg:items-center">
                    <div>
                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">{{ $formattedCompetitionType }}</span>
                        <h1 class="mt-4 text-3xl font-extrabold uppercase tracking-tight md:text-4xl">{{ $event->name }}</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-relaxed text-blue-100">
                            {{ $event->start_date?->format('d M Y') ?? '-' }} sampai {{ $event->end_date?->format('d M Y') ?? '-' }} · U-{{ $event->min_age ?? '?' }} s/d U-{{ $event->max_age ?? '?' }}
                        </p>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-2xl border border-white/20 bg-white/10 px-3 py-4 text-center backdrop-blur">
                            <p class="text-2xl font-extrabold text-white">{{ $participantClubs->count() }}</p>
                            <p class="mt-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-blue-100">Klub</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 px-3 py-4 text-center backdrop-blur">
                            <p class="text-2xl font-extrabold text-white">{{ $matches->count() }}</p>
                            <p class="mt-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-blue-100">Pertandingan</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 px-3 py-4 text-center backdrop-blur">
                            <p class="text-2xl font-extrabold text-white">{{ $finishedMatches->count() }}</p>
                            <p class="mt-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-blue-100">Selesai</p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ tab: 'peserta' }" class="mt-8 space-y-6">
                <div class="home-filter-bar">
                    <button type="button" @click="tab = 'peserta'" :class="tab === 'peserta' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Peserta</button>
                    <button type="button" @click="tab = 'jadwal'" :class="tab === 'jadwal' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Jadwal & Hasil</button>
                    @if ($isLeague)
                        <button type="button" @click="tab = 'klasemen'" :class="tab === 'klasemen' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Klasemen</button>
                    @endif
                    @if ($isKnockout)
                        <button type="button" @click="tab = 'bagan'" :class="tab === 'bagan' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Bagan</button>
                    @endif
                    <button type="button" @click="tab = 'statistik'" :class="tab === 'statistik' ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill'">Statistik</button>
                </div>

                <section x-show="tab === 'peserta'" x-cloak>
                    <x-public.section-heading eyebrow="Peserta" title="Klub Peserta" subtitle="Klub yang terdaftar pada event ini." />

                    @if ($participantClubs->isNotEmpty())
                        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @foreach ($participantClubs as $club)
                                <article class="home-club-card">
                                    <x-public.club-badge :club="$club" size="lg" />
                                    <h3 class="mt-3 text-sm font-bold text-slate-900">{{ $club->name }}</h3>
                                    <p class="mt-1 text-xs text-slate-500">Kode: {{ $club->code ?? '-' }}</p>
                                    <a href="{{ route('public.teams.index', ['search' => $club->name]) }}" class="mt-4 text-xs font-bold uppercase tracking-wide text-red-600 hover:text-red-800">Lihat Profil</a>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                            <p class="text-sm text-slate-500">Belum ada peserta terdaftar.</p>
                        </div>
                    @endif
                </section>

                <section x-show="tab === 'jadwal'" x-cloak>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="home-data-card-flush h-full">
                            <x-public.section-heading
                                eyebrow="Jadwal"
                                title="Jadwal Mendatang"
                                subtitle="Pertandingan yang belum dimainkan."
                                class="!px-5 !pt-5"
                            />
                            <div class="home-list">
                                @forelse ($upcomingMatches as $match)
                                    <div class="home-fixture-row">
                                        <div class="home-fixture-date">
                                            <span class="home-fixture-day">{{ $match->match_date->format('d') }}</span>
                                            <span class="home-fixture-month">{{ $idMonth($match->match_date) }}</span>
                                        </div>
                                        <div class="home-fixture-teams">
                                            <p>{{ $match->homeClub?->name }} <span class="text-slate-400">vs</span> {{ $match->awayClub?->name }}</p>
                                            <p class="home-fixture-meta">{{ $match->match_date->format('H:i') }} WIB · {{ $match->stadium?->name ?? 'Stadion belum ditentukan' }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-10 text-center">
                                        <p class="text-sm text-slate-500">Belum ada jadwal mendatang.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="home-data-card-flush h-full">
                            <x-public.section-heading
                                eyebrow="Hasil"
                                title="Hasil Terakhir"
                                subtitle="Pertandingan yang sudah selesai."
                                class="!px-5 !pt-5"
                            />
                            <div class="home-list">
                                @forelse ($finishedMatches as $match)
                                    <div class="home-fixture-row">
                                        <div class="home-fixture-date">
                                            <span class="home-fixture-day">{{ $match->match_date->format('d') }}</span>
                                            <span class="home-fixture-month">{{ $idMonth($match->match_date) }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm font-semibold text-slate-800">
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
                </section>

                @if ($isLeague)
                    <section x-show="tab === 'klasemen'" x-cloak>
                        <x-public.section-heading
                            eyebrow="Klasemen"
                            title="Klasemen Liga"
                            subtitle="Peringkat tim berdasarkan hasil pertandingan pada event ini."
                        />

                        @if ($hasStandings)
                            <div class="home-data-card-flush mt-5">
                                <div class="home-table-wrap">
                                    <table class="home-table">
                                        <thead>
                                            <tr>
                                                <th class="home-table-pos">#</th>
                                                <th class="text-left">Tim</th>
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
                                                    $gd = ($row['gf'] ?? 0) - ($row['ga'] ?? 0);
                                                @endphp
                                                <tr>
                                                    <td class="home-table-pos">{{ $idx + 1 }}</td>
                                                    <td>
                                                        <div class="flex items-center gap-3">
                                                            <x-public.club-badge :name="$row['club_name'] ?? null" :code="$row['club_code'] ?? null" :color="$row['shirt_color'] ?? null" />
                                                            <span class="text-sm font-semibold text-slate-900">{{ $row['club_name'] }}</span>
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
                            </div>
                        @else
                            <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                                <p class="text-sm text-slate-500">Belum ada data klasemen untuk event ini.</p>
                            </div>
                        @endif
                    </section>
                @endif

                @if ($isKnockout)
                    <section x-show="tab === 'bagan'" x-cloak>
                        <x-public.section-heading eyebrow="Bagan" title="Alur Pertandingan" subtitle="Bagan knockout yang sudah disiapkan panitia." />

                        @if ($event->bracketRounds->isNotEmpty())
                            <div class="mt-5 overflow-x-auto pb-2">
                                <div class="flex min-w-max gap-6">
                                    @foreach ($event->bracketRounds as $round)
                                        <div class="flex w-60 shrink-0 flex-col">
                                            <p class="border-b-2 border-red-600 pb-2 text-center text-xs font-bold uppercase tracking-wide text-slate-700">
                                                {{ $round->name }}
                                            </p>
                                            <div class="mt-3 flex flex-1 flex-col justify-around gap-3">
                                                @foreach ($round->matches as $match)
                                                    @php
                                                        $isWinnerHome = $match->winner_club_id !== null && $match->winner_club_id === $match->home_club_id;
                                                        $isWinnerAway = $match->winner_club_id !== null && $match->winner_club_id === $match->away_club_id;
                                                    @endphp
                                                    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                                                        <p class="text-center text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">Match {{ $match->position }}</p>
                                                        <div class="mt-2 space-y-1">
                                                            <div class="flex items-center justify-between gap-2 rounded-lg px-2 py-1 text-sm {{ $isWinnerHome ? 'bg-emerald-50 font-semibold text-emerald-800' : ($match->homeClub ? 'text-slate-700' : 'text-slate-300') }}">
                                                                <span class="truncate">{{ $match->homeClub?->name ?? 'Menunggu tim' }}</span>
                                                            </div>
                                                            <div class="flex items-center justify-between gap-2 rounded-lg px-2 py-1 text-sm {{ $isWinnerAway ? 'bg-emerald-50 font-semibold text-emerald-800' : ($match->awayClub ? 'text-slate-700' : 'text-slate-300') }}">
                                                                <span class="truncate">{{ $match->awayClub?->name ?? ($match->homeClub ? 'Bye' : 'Menunggu tim') }}</span>
                                                            </div>
                                                        </div>
                                                        <p class="mt-2 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $match->statusLabel() }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($matches->isNotEmpty())
                            <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                                <p class="text-sm text-slate-500">Bagan knockout belum dibuat oleh panitia. Daftar pertandingan ditampilkan sebagai daftar.</p>
                            </div>

                            <div class="home-data-card-flush mt-4">
                                <div class="home-list">
                                    @foreach ($matches as $match)
                                        <div class="home-fixture-row">
                                            <div class="home-fixture-date">
                                                <span class="home-fixture-day">{{ $match->match_date->format('d') }}</span>
                                                <span class="home-fixture-month">{{ $idMonth($match->match_date) }}</span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $match->homeClub?->name }} <span class="text-slate-400">vs</span> {{ $match->awayClub?->name }}
                                                </p>
                                                <p class="home-fixture-meta">{{ $match->match_date->format('H:i') }} WIB · {{ $match->stadium?->name ?? 'Stadion belum ditentukan' }}</p>
                                            </div>
                                            <span class="home-score-box">
                                                {{ $match->result?->home_score ?? '-' }}
                                                <span class="text-slate-400">-</span>
                                                {{ $match->result?->away_score ?? '-' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                                <p class="text-sm text-slate-500">Bagan belum tersedia karena belum ada pertandingan.</p>
                            </div>
                        @endif
                    </section>
                @endif

                <section x-show="tab === 'statistik'" x-cloak>
                    <x-public.section-heading eyebrow="Statistik" title="Top Skor dan Kartu" subtitle="Ringkasan statistik pemain di event ini." />

                    <div class="mt-5 grid gap-6 lg:grid-cols-3">
                        @php
                            $statPanels = [
                                ['title' => 'Top Skor', 'rows' => $topScorers, 'label' => 'Gol', 'tone' => 'home-form-win', 'nameKey' => 'name', 'valueKey' => 'total_goals', 'subKey' => 'club_name'],
                                ['title' => 'Kartu Kuning', 'rows' => $yellowCards, 'label' => 'Kartu', 'tone' => 'bg-amber-400', 'nameKey' => 'player_name', 'valueKey' => 'total_cards', 'subKey' => 'club_name'],
                                ['title' => 'Kartu Merah', 'rows' => $redCards, 'label' => 'Kartu', 'tone' => 'bg-rose-500', 'nameKey' => 'player_name', 'valueKey' => 'total_cards', 'subKey' => 'club_name'],
                            ];
                        @endphp

                        @foreach ($statPanels as $panel)
                            <div class="home-data-card-flush h-full">
                                <x-public.section-heading :title="$panel['title']" class="!px-5 !pt-5" />
                                <div class="home-list">
                                    @forelse ($panel['rows'] as $idx => $row)
                                        <div class="home-scorer-row">
                                            <span class="home-rank-badge {{ $panel['tone'] }}">{{ $idx + 1 }}</span>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-semibold text-slate-900">{{ $row->{$panel['nameKey']} ?? 'Pemain' }}</p>
                                                <p class="truncate text-xs text-slate-500">{{ $row->{$panel['subKey']} ?? 'Tim' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-base font-extrabold tabular-nums text-slate-900">{{ $row->{$panel['valueKey']} ?? 0 }}</p>
                                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $panel['label'] }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-10 text-center">
                                            <p class="text-sm text-slate-500">Belum ada data.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </section>
    </main>
</x-public-layout>
