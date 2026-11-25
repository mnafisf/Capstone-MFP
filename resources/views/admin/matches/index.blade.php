<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Manajemen Pertandingan</h1>
                <p class="mt-1 text-sm text-blue-100">
                    Kelola jadwal pertandingan, lalu buka halaman detail untuk mengisi gol, kartu, dan pergantian pemain.
                </p>
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

                <button
                    type="button"
                    class="btn-primary"
                    x-on:click="$dispatch('open-modal', 'create-match')"
                >
                    Tambah Pertandingan
                </button>
            </div>

            <form
                method="GET"
                action="{{ route('admin.matches.index') }}"
                class="mt-4 flex flex-wrap items-end gap-3"
            >
                @if (request()->filled('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div>
                    <label for="event_id" class="label">Filter Event</label>
                    <select id="event_id" name="event_id" class="input">
                        <option value="">Semua Event</option>

                        @foreach ($events as $event)
                            <option
                                value="{{ $event->id }}"
                                @selected((string) request('event_id') === (string) $event->id)
                            >
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-secondary">
                    Filter
                </button>

                @if (request()->filled('event_id'))
                    <a href="{{ route('admin.matches.index') }}" class="btn-secondary">
                        Reset
                    </a>
                @endif
            </form>

            @if ($matches->isEmpty())
                @if (request()->filled('event_id'))
                    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Belum ada pertandingan untuk event yang dipilih.
                        <a
                            href="{{ route('admin.matches.index') }}"
                            class="font-semibold underline"
                        >
                            Tampilkan semua pertandingan
                        </a>
                    </p>
                @else
                    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Belum ada jadwal pertandingan. Klik
                        <strong>Tambah Pertandingan</strong> untuk membuat jadwal baru.
                    </p>
                @endif
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
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        {{ $match->event->name }}
                                        <span class="ml-2 inline-flex rounded-full bg-blue-100 px-2 py-1 normal-case text-blue-800">
                                            Pertandingan {{ $match->event_match_number }}
                                        </span>
                                    </p>

                                    <p class="mt-1 text-lg font-bold text-slate-900">
                                        {{ $match->homeClub->name }}
                                        <span class="mx-1 text-slate-400">vs</span>
                                        {{ $match->awayClub->name }}
                                    </p>

                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $match->match_date->format('d M Y, H:i') }} WIB
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Deadline lineup:
                                        {{ $match->lineup_deadline?->format('d M Y H:i') ?? '-' }}
                                    </p>
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
                                        <p>
                                            {{ $match->stadium?->name ?: 'Stadion belum diatur' }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{ $match->referee?->name ?: 'Wasit belum diatur' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $matches->links() }}
                </div>
            @endif
        </div>

        <x-modal name="create-match" maxWidth="lg">
            <div class="p-6">
                <h2 class="text-lg font-bold text-slate-900">
                    Tambah Pertandingan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Buat jadwal pertandingan baru.
                </p>

                <form
                    method="POST"
                    action="{{ route('admin.matches.store') }}"
                    class="mt-4 space-y-3"
                    x-data="{
                        eventParticipants: @js($eventParticipants),
                        clubs: @js($clubs),
                        selectedEventId: '',
                        selectedHomeClubId: '',
                        selectedAwayClubId: '',
                        selectedRefereeId: '',
                        selectedReferee2Id: '',
                        selectedReferee3Id: '',

                        allowedClubs() {
                            if (!this.selectedEventId) return [];

                            const participantIds = this.eventParticipants[this.selectedEventId] ?? [];

                            return this.clubs.filter(club =>
                                participantIds.includes(Number(club.id))
                            );
                        }
                    }"
                >
                    @csrf

                    <!-- Event -->
                    <div>
                        <label class="label">Event</label>

                        <select
                            name="event_id"
                            class="input"
                            x-model="selectedEventId"
                            required
                        >
                            <option value="">Pilih Event</option>

                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Club A -->
                    <div>
                        <label class="label">Club A (Tuan Rumah)</label>

                        <select
                            name="home_club_id"
                            class="input"
                            x-model="selectedHomeClubId"
                            required
                        >
                            <option value="">Pilih Club</option>

                            <template x-for="club in allowedClubs()" :key="club.id">
                                <option :value="club.id" x-text="club.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Club B -->
                    <div>
                        <label class="label">Club B (Tamu)</label>

                        <select
                            name="away_club_id"
                            class="input"
                            x-model="selectedAwayClubId"
                            required
                        >
                            <option value="">Pilih Club</option>

                            <template x-for="club in allowedClubs()" :key="club.id">
                                <option
                                    :value="club.id"
                                    x-text="club.name"
                                    :disabled="String(club.id) === String(selectedHomeClubId)"
                                ></option>
                            </template>
                        </select>
                    </div>

                    <!-- Stadion -->
                    <div>
                        <label class="label">Stadion</label>

                        <select name="stadium_id" class="input">
                            <option value="">-</option>

                            @foreach ($stadiums as $stadium)
                                <option value="{{ $stadium->id }}">
                                    {{ $stadium->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Wasit -->
                    <div>
                        <label class="label">Wasit</label>

                        <select name="referee_id" class="input" x-model="selectedRefereeId">
                            <option value="">-</option>

                            @foreach ($referees as $ref)
                                <option value="{{ $ref->id }}">{{ $ref->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Asisten Wasit 1 -->
                    <div>
                        <label class="label">Asisten Wasit 1</label>

                        <select name="referee_2_id" class="input" x-model="selectedReferee2Id">
                            <option value="">-</option>

                            @foreach ($referees as $ref)
                                <option
                                    value="{{ $ref->id }}"
                                    @disabled(false)
                                    x-bind:disabled="String({{ $ref->id }}) === String(selectedRefereeId) || String({{ $ref->id }}) === String(selectedReferee3Id)"
                                >
                                    {{ $ref->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Asisten Wasit 2 -->
                    <div>
                        <label class="label">Asisten Wasit 2</label>

                        <select name="referee_3_id" class="input" x-model="selectedReferee3Id">
                            <option value="">-</option>

                            @foreach ($referees as $ref)
                                <option
                                    value="{{ $ref->id }}"
                                    x-bind:disabled="String({{ $ref->id }}) === String(selectedRefereeId) || String({{ $ref->id }}) === String(selectedReferee2Id)"
                                >
                                    {{ $ref->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal dan Waktu -->
                    <div>
                        <label class="label">Tanggal Waktu</label>

                        <input
                            type="datetime-local"
                            name="match_date"
                            class="input"
                            required
                        >
                    </div>

                    <!-- Tombol -->
                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            class="btn-secondary"
                            x-on:click="$dispatch('close-modal', 'create-match')"
                        >
                            Batal
                        </button>

                        <button class="btn-primary" type="submit">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>