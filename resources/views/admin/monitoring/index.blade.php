<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight">
                        @if ($event)
                            Monitoring Event: {{ $event->name }}
                        @else
                            Monitoring Kompetisi
                        @endif
                    </h1>
                    <p class="mt-1 text-sm text-blue-100">Pantau klasemen, top skor, dan riwayat pertandingan secara otomatis.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <h2 class="panel-title">Pilih Event</h2>
        <p class="panel-subtitle">Pilih event yang ingin dipantau.</p>
        <form method="GET" action="{{ route('admin.monitoring.index') }}" class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="min-w-0 flex-1">
                <label class="label" for="event_id">Event</label>
                <select id="event_id" name="event_id" class="input" required>
                    <option value="">— Pilih event —</option>
                    @foreach ($events as $item)
                        <option value="{{ $item->id }}" @selected($event?->id === $item->id)>
                            {{ $item->name }} ({{ strtoupper($item->competition_type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="action-frame w-full shrink-0 lg:w-auto">
                <button type="submit" class="btn-primary">Tampilkan</button>
                <a href="{{ route('admin.events.index') }}" class="btn">Kembali ke Event</a>
            </div>
        </form>
    </div>

    @if (! $event)
        <div class="card mt-6">
            @if ($events->isEmpty())
                <p class="text-sm text-slate-600">Belum ada event. Buat event terlebih dahulu di menu <a href="{{ route('admin.events.index') }}" class="font-semibold text-blue-600 hover:underline">Event</a>.</p>
            @else
                <p class="text-sm text-slate-600">Pilih event di atas untuk melihat klasemen, top skor, dan riwayat pertandingan.</p>
            @endif
        </div>
    @else
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <div class="card">
                <h2 class="text-lg font-semibold">Klasemen Otomatis</h2>
                <div class="table-wrap mt-3">
                    <table>
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="p-2">Klub</th>
                                <th class="p-2">P</th>
                                <th class="p-2">W</th>
                                <th class="p-2">D</th>
                                <th class="p-2">L</th>
                                <th class="p-2">Pts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($standings as $row)
                                <tr class="table-row-hover border-t">
                                    <td class="p-2">{{ $clubs[$row['club_id']] ?? 'Klub #' . $row['club_id'] }}</td>
                                    <td class="p-2">{{ $row['played'] }}</td>
                                    <td class="p-2">{{ $row['win'] }}</td>
                                    <td class="p-2">{{ $row['draw'] }}</td>
                                    <td class="p-2">{{ $row['loss'] }}</td>
                                    <td class="p-2 font-semibold">{{ $row['points'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-sm text-slate-500">Belum ada hasil pertandingan untuk event ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h2 class="text-lg font-semibold">Top Skor Otomatis</h2>
                <div class="table-wrap mt-3">
                    <table>
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="p-2">Pemain</th>
                                <th class="p-2">Gol</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topScorers as $scorer)
                                <tr class="table-row-hover border-t">
                                    <td class="p-2">{{ $scorer->name }}</td>
                                    <td class="p-2 font-semibold">{{ $scorer->total_goals }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="p-4 text-center text-sm text-slate-500">Belum ada data gol.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-6">
            <h2 class="text-lg font-semibold">Riwayat Pertandingan</h2>
            <div class="table-wrap mt-3">
                <table>
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="p-2">Pertandingan</th>
                            <th class="p-2">Skor</th>
                            <th class="p-2">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historyMatches as $match)
                            <tr class="table-row-hover border-t align-top">
                                <td class="p-2">{{ $match->homeClub->name }} vs {{ $match->awayClub->name }}</td>
                                <td class="max-w-md p-2">
                                    <div class="font-semibold">{{ $match->result->home_score ?? 0 }} - {{ $match->result->away_score ?? 0 }}</div>
                                    @include('partials.match-goal-scorers', ['match' => $match])
                                </td>
                                <td class="p-2">{{ $match->match_date->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-sm text-slate-500">Belum ada pertandingan untuk event ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
