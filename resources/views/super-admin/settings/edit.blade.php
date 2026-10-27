<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Super Admin</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Atur Deadline Lineup</h1>
                <p class="mt-1 text-sm text-blue-100">{{ $match->homeClub->name }} vs {{ $match->awayClub->name }} · {{ $match->match_date->format('d M Y H:i') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="surface-red-blue max-w-3xl">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 class="panel-title">Deadline Input Lineup</h2>
                <p class="panel-subtitle">Batas akhir ini berlaku hanya untuk pertandingan di atas.</p>
            </div>
            <a href="{{ route('super-admin.settings.index', request()->only('event_id')) }}" class="btn-secondary">Kembali ke Jadwal</a>
        </div>
        <div class="mb-4 rounded-xl border border-blue-100 bg-blue-50 p-3 text-sm text-blue-700">
            Pastikan deadline lebih awal dari kickoff agar club admin hanya bisa mengisi lineup sebelum pertandingan dimulai.
        </div>

        <form method="POST" action="{{ route('super-admin.settings.update', $match) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="label">Pertandingan</label>
                <p class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800">{{ $match->homeClub->name }} vs {{ $match->awayClub->name }} ({{ $match->match_date->format('d M Y H:i') }})</p>
            </div>
            <div>
                <label class="label">Deadline Input Lineup</label>
                <input type="datetime-local" name="lineup_deadline" class="input" value="{{ old('lineup_deadline', optional($match->lineup_deadline)->format('Y-m-d\TH:i')) }}" required>
                @error('lineup_deadline')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center gap-2">
                <button class="btn-primary" type="submit">Simpan Deadline</button>
                <a href="{{ route('super-admin.settings.index', request()->only('event_id')) }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>