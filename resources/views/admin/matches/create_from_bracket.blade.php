<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Buat Jadwal dari Bagan</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Jadwalkan Pertandingan</h1>
                <p class="mt-1 text-sm text-blue-100">{{ $bracketMatch->homeClub?->name }} vs {{ $bracketMatch->awayClub?->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="card">
            <h2 class="panel-title">Detail Pertandingan</h2>
            <form method="POST" action="{{ route('admin.matches.store') }}" class="mt-4 space-y-4 max-w-lg">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="hidden" name="event_bracket_match_id" value="{{ $bracketMatch->id }}">
                <input type="hidden" name="home_club_id" value="{{ $bracketMatch->home_club_id }}">
                <input type="hidden" name="away_club_id" value="{{ $bracketMatch->away_club_id }}">

                <div>
                    <label class="label">Event</label>
                    <input type="text" class="input bg-slate-100 text-slate-500" value="{{ $event->name }}" readonly disabled>
                </div>
                <div>
                    <label class="label">Tim Kandang (Home)</label>
                    <input type="text" class="input bg-slate-100 text-slate-500" value="{{ $bracketMatch->homeClub?->name }}" readonly disabled>
                </div>
                <div>
                    <label class="label">Tim Tamu (Away)</label>
                    <input type="text" class="input bg-slate-100 text-slate-500" value="{{ $bracketMatch->awayClub?->name }}" readonly disabled>
                </div>

                <div><label class="label">Stadion</label><select name="stadium_id" class="input"><option value="">-</option>@foreach($stadiums as $stadium)<option value="{{ $stadium->id }}">{{ $stadium->name }}</option>@endforeach</select></div>
                <div><label class="label">Wasit</label><select name="referee_id" class="input"><option value="">-</option>@foreach($referees as $ref)<option value="{{ $ref->id }}">{{ $ref->name }}</option>@endforeach</select></div>
                <div><label class="label">Asisten Wasit 1</label><select name="referee_2_id" class="input"><option value="">-</option>@foreach($referees as $ref)<option value="{{ $ref->id }}">{{ $ref->name }}</option>@endforeach</select></div>
                <div><label class="label">Asisten Wasit 2</label><select name="referee_3_id" class="input"><option value="">-</option>@foreach($referees as $ref)<option value="{{ $ref->id }}">{{ $ref->name }}</option>@endforeach</select></div>
                <div><label class="label">Tanggal Waktu</label><input type="datetime-local" name="match_date" class="input" required></div>
                <div><label class="label">Batas Susunan Pemain (Lineup Deadline)</label><input type="datetime-local" name="lineup_deadline" class="input"></div>

                <div class="flex justify-end gap-2 pt-4">
                    <a href="{{ route('admin.events.teams.index', $event) }}" class="btn-secondary">Batal</a>
                    <button class="btn-primary" type="submit">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
