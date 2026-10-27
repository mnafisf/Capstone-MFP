<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Manajemen Pertandingan</h1>
                <p class="mt-1 text-sm text-blue-100">Kelola jadwal pertandingan, lalu buka halaman detail untuk mengisi gol, kartu, dan pergantian pemain.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            <div class="toolbar">
                <div>
                    <h2 class="panel-title">Jadwal Pertandingan</h2>
                    <p class="panel-subtitle">Pilih pertandingan untuk membuka halaman detail terpisah.</p>
                </div>
                <button type="button" class="btn-primary" x-on:click="$dispatch('open-modal', 'create-match')">
                    Tambah Pertandingan
                </button>
            </div>

            @if ($matches->isEmpty())
                <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Belum ada jadwal pertandingan. Klik <strong>Tambah Pertandingan</strong> untuk membuat jadwal baru.
                </p>
            @else
                <div class="mt-4 space-y-2">
                    @foreach ($matches as $match)
                        <button
                            type="button"
                            class="w-full rounded-xl border border-slate-200 bg-white p-4 text-left transition hover:border-blue-300 hover:bg-blue-50/50"
                            x-on:click="window.location='{{ route('admin.matches.show', $match) }}'"
                        >
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $match->event->name }}</p>
                                    <p class="mt-1 text-lg font-bold text-slate-900">
                                        {{ $match->homeClub->name }}
                                        <span class="mx-1 text-slate-400">vs</span>
                                        {{ $match->awayClub->name }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $match->match_date->format('d M Y, H:i') }} WIB</p>
                                    <p class="mt-1 text-xs text-slate-500">Deadline lineup: {{ $match->lineup_deadline?->format('d M Y H:i') ?? '-' }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="rounded-lg bg-slate-100 px-4 py-2 text-center">
                                        <p class="text-2xl font-bold text-slate-900">
                                            {{ $match->result->home_score ?? 0 }}
                                            <span class="text-slate-400">-</span>
                                            {{ $match->result->away_score ?? 0 }}
                                        </p>
                                        <p class="text-xs text-slate-500">Skor</p>
                                    </div>
                                    <div class="text-right text-sm text-slate-600">
                                        <p>{{ $match->stadium?->name ?: 'Stadion belum diatur' }}</p>
                                        <p class="text-xs text-slate-500">{{ $match->referee?->name ?: 'Wasit belum diatur' }}</p>
                                    </div>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="mt-4">{{ $matches->links() }}</div>
            @endif
        </div>

        <x-modal name="create-match" maxWidth="lg">
            <div class="p-6">
                <h2 class="text-lg font-bold text-slate-900">Tambah Pertandingan</h2>
                <p class="mt-1 text-sm text-slate-500">Buat jadwal pertandingan baru.</p>

                <form method="POST" action="{{ route('admin.matches.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <div><label class="label">Event</label><select name="event_id" class="input">@foreach($events as $event)<option value="{{ $event->id }}">{{ $event->name }}</option>@endforeach</select></div>
                    <div><label class="label">Club A</label><select name="home_club_id" class="input">@foreach($clubs as $club)<option value="{{ $club->id }}">{{ $club->name }}</option>@endforeach</select></div>
                    <div><label class="label">Club B</label><select name="away_club_id" class="input">@foreach($clubs as $club)<option value="{{ $club->id }}">{{ $club->name }}</option>@endforeach</select></div>
                    <div><label class="label">Stadion</label><select name="stadium_id" class="input"><option value="">-</option>@foreach($stadiums as $stadium)<option value="{{ $stadium->id }}">{{ $stadium->name }}</option>@endforeach</select></div>
                    <div><label class="label">Wasit</label><select name="referee_id" class="input"><option value="">-</option>@foreach($referees as $ref)<option value="{{ $ref->id }}">{{ $ref->name }}</option>@endforeach</select></div>
                    <div><label class="label">Asisten Wasit 1</label><select name="referee_2_id" class="input"><option value="">-</option>@foreach($referees as $ref)<option value="{{ $ref->id }}">{{ $ref->name }}</option>@endforeach</select></div>
                    <div><label class="label">Asisten Wasit 2</label><select name="referee_3_id" class="input"><option value="">-</option>@foreach($referees as $ref)<option value="{{ $ref->id }}">{{ $ref->name }}</option>@endforeach</select></div>
                    <div><label class="label">Tanggal Waktu</label><input type="datetime-local" name="match_date" class="input" required></div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="btn-secondary" x-on:click="$dispatch('close-modal', 'create-match')">Batal</button>
                        <button class="btn-primary" type="submit">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>
