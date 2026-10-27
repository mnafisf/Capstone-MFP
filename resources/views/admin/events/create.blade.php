<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Admin Panel</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Tambah Event</h1>
                <p class="mt-1 text-sm text-blue-100">Buat event kompetisi baru.</p>
            </div>
        </div>
    </x-slot>

    <div class="card max-w-2xl">
        <h2 class="panel-title">Form Tambah Event</h2>
        <p class="panel-subtitle">Isi informasi event kompetisi.</p>

        <form method="POST" action="{{ route('admin.events.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="label">Nama Event</label>
                <input name="name" class="input" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="label">Tipe Kompetisi</label>
                <select name="competition_type" class="input" required>
                    <option value="league" @selected(old('competition_type') === 'league')>Liga</option>
                    <option value="knockout" @selected(old('competition_type') === 'knockout')>Knockout</option>
                </select>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="label">Min Umur</label>
                    <input type="number" name="min_age" class="input" value="{{ old('min_age') }}" min="1" max="60">
                </div>
                <div>
                    <label class="label">Max Umur</label>
                    <input type="number" name="max_age" class="input" value="{{ old('max_age') }}" min="1" max="60">
                </div>
                <div>
                    <label class="label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="input" value="{{ old('start_date') }}">
                </div>
                <div>
                    <label class="label">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="input" value="{{ old('end_date') }}">
                </div>
            </div>

            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <h3 class="text-sm font-semibold text-amber-900">Aturan Disiplin Kartu</h3>
                <p class="mt-1 text-xs text-amber-800">Akumulasi kartu kuning dihitung per event. Setelah hukuman selesai, hitungan kartu kuning direset ke 0.</p>
                <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="label">Batas akumulasi kartu kuning (Di Event)</label>
                        <input type="number" name="yellow_card_threshold" class="input" value="{{ old('yellow_card_threshold', 3) }}" min="1" max="20" required>
                    </div>
                    <div>
                        <label class="label">Larangan akumulasi kuning (pertandingan)</label>
                        <input type="number" name="yellow_card_ban_matches" class="input" value="{{ old('yellow_card_ban_matches', 1) }}" min="1" max="10" required>
                    </div>
                    <div>
                        <label class="label">Larangan kartu merah (pertandingan)</label>
                        <input type="number" name="red_card_ban_matches" class="input" value="{{ old('red_card_ban_matches', 1) }}" min="1" max="10" required>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <a class="btn" href="{{ route('admin.events.index') }}">Batal</a>
                <button class="btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</x-app-layout>
