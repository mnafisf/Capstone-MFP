<x-public-layout title="SAPHYR - Klasemen & Top Skor" active-section="klasemen">
    <main class="pb-16">
        <section class="home-section">
            <x-public.section-heading
                eyebrow="Liga & Kompetisi"
                title="Klasemen & Top Skor"
                subtitle="Peringkat klub dan pemain terbaik untuk setiap kompetisi."
            />

            @if ($availableEvents->isNotEmpty())
                <div class="home-filter-bar mt-5">
                    @foreach ($availableEvents as $event)
                        <a
                            href="{{ route('public.standings.index', ['event_id' => $event->id]) }}"
                            class="{{ $eventId === $event->id ? 'home-filter-pill home-filter-pill-active' : 'home-filter-pill' }}"
                        >{{ $event->name }}</a>
                    @endforeach
                </div>
            @endif

            @if ($selectedEvent)
                <div class="mt-6 grid gap-6 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <div class="home-data-card-flush h-full">
                            <x-public.section-heading
                                eyebrow="Peringkat"
                                title="Klasemen"
                                :subtitle="$selectedEvent->name"
                                class="!px-5 !pt-5"
                            />

                            @if ($standings->isNotEmpty())
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
                            @else
                                <div class="rounded-b-2xl border-t border-dashed border-slate-200 p-10 text-center">
                                    <p class="text-sm text-slate-500">Belum ada pertandingan yang selesai pada event ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="home-data-card-flush h-full">
                            <x-public.section-heading
                                eyebrow="Golden Boot"
                                title="Top Skor"
                                subtitle="Pemain dengan gol terbanyak."
                                class="!px-5 !pt-5"
                            />
                            <div class="home-list">
                                @forelse ($topScorers as $idx => $scorer)
                                    <div class="home-scorer-row">
                                        <span class="home-rank-badge {{ $idx === 0 ? 'home-rank-gold' : ($idx === 1 ? 'home-rank-silver' : ($idx === 2 ? 'home-rank-bronze' : '')) }}">{{ $idx + 1 }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-slate-900">{{ $scorer->name ?? $scorer->player_name ?? 'Pemain' }}</p>
                                            <p class="truncate text-xs text-slate-500">{{ $scorer->club_name ?? 'Tim' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-base font-extrabold tabular-nums text-slate-900">{{ $scorer->total_goals ?? $scorer->goals ?? 0 }}</p>
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
            @else
                <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                    <p class="text-sm text-slate-500">Belum ada event dengan data klasemen.</p>
                </div>
            @endif
        </section>
    </main>
</x-public-layout>
