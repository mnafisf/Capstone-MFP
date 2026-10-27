<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Detail Pertandingan</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">{{ $match->homeClub->name }} vs {{ $match->awayClub->name }}</h1>
                <p class="mt-1 text-sm text-blue-100">Halaman ini dipakai untuk input gol, kartu, dan pergantian pemain.</p>
            </div>
        </div>
    </x-slot>

    @php
        $homeGoals = $match->goals->where('club_id', $match->home_club_id);
        $awayGoals = $match->goals->where('club_id', $match->away_club_id);
        $homeCards = $match->cards->where('club_id', $match->home_club_id);
        $awayCards = $match->cards->where('club_id', $match->away_club_id);
        $homeSubs = $match->substitutions->where('club_id', $match->home_club_id);
        $awaySubs = $match->substitutions->where('club_id', $match->away_club_id);
        $homeCurrentStarters = $homeStarters->whereIn('player_id', $homeActiveStarterIds)->values();
        $awayCurrentStarters = $awayStarters->whereIn('player_id', $awayActiveStarterIds)->values();
    @endphp

    <div class="space-y-6">
        <div class="card">
            <div class="toolbar">
                <div>
                    <h2 class="panel-title">Skor Otomatis</h2>
                    <p class="panel-subtitle">Skor diambil dari total gol per tim dan akan ikut berubah saat gol ditambah atau dihapus.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.matches.index') }}" class="btn-secondary">Kembali ke Jadwal</a>
                    <a href="{{ route('admin.matches.dsp.download', $match) }}" class="btn-secondary">Lihat DSP</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 md:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pertandingan</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $match->event->name }}</p>
                    <p class="text-sm text-slate-600">{{ $match->match_date->format('d M Y, H:i') }} WIB</p>
                    <p class="mt-1 text-xs text-slate-500">Deadline lineup: {{ $match->lineup_deadline?->format('d M Y H:i') ?? '-' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Stadion</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $match->stadium?->name ?: '-' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Wasit</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ $match->referee?->name ?: '-' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $match->referee2?->name ?: '-' }} | {{ $match->referee3?->name ?: '-' }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">{{ $match->homeClub->name }}</p>
                    <p class="mt-2 text-5xl font-black text-blue-900">{{ $match->result?->home_score ?? 0 }}</p>
                    <p class="text-sm text-blue-700">Gol, kartu, dan substitusi tim tuan rumah ada di kolom kiri.</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-right">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">{{ $match->awayClub->name }}</p>
                    <p class="mt-2 text-5xl font-black text-amber-900">{{ $match->result?->away_score ?? 0 }}</p>
                    <p class="text-sm text-amber-700">Gol, kartu, dan substitusi tim tamu ada di kolom kanan.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="card border-l-4 border-l-blue-600">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="panel-title">{{ $match->homeClub->name }}</h2>
                        <p class="panel-subtitle">Panel kiri untuk tim kandang.</p>
                    </div>
                    <span class="chip-soft">Home</span>
                </div>

                <div class="mt-6 space-y-6">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Gol</p>
                            <span class="text-xs text-slate-500">{{ $homeGoals->count() }} gol</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            @forelse ($homeGoals as $goal)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $goal->player?->name }}</p>
                                        <p class="text-xs text-slate-500">Menit {{ $goal->minute_label }}@if ($goal->is_penalty) · Penalti @endif</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.matches.goals.destroy', [$match, $goal]) }}" onsubmit="return confirm('Hapus gol ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada gol.</p>
                            @endforelse
                        </div>
                        <form method="POST" action="{{ route('admin.matches.goals.store', $match) }}" class="mt-3 space-y-3 rounded-lg border border-blue-100 bg-blue-50/50 p-3">
                            @csrf
                            <input type="hidden" name="club_id" value="{{ $match->home_club_id }}">
                            <p class="text-xs text-slate-500">Pemain diambil dari lineup yang sudah disusun klub admin. Pemain approved lain juga tersedia.</p>
                            <div>
                                <label class="label">Pemain</label>
                                <select name="player_id" class="input w-full" required>
                                    <option value="">Pilih pemain</option>
                                    @foreach ($homeSelectPlayers as $p)
                                        <option value="{{ $p['id'] }}">
                                            {{ $p['name'] }}
                                            @if ($p['in_lineup'])
                                                {{ $p['position_type'] === 'starter' ? '(Starter)' : '(Masuk dari cadangan)' }}
                                            @else
                                                (Pemain approved)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Menit</label>
                                <input type="text" name="minute_label" class="input w-full" placeholder="24 atau 45+5" required pattern="[0-9]{1,3}(\+[0-9]{1,2})?" title="Gunakan format menit seperti 24 atau 45+5">
                            </div>
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="is_penalty" value="1" class="rounded border-slate-300">
                                Penalti
                            </label>
                            <button type="submit" class="btn-primary w-full">Tambah Gol Home</button>
                        </form>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kartu</p>
                            <span class="text-xs text-slate-500">{{ $homeCards->count() }} kartu</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            @forelse ($homeCards as $card)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $card->player?->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $card->card_type === 'red' ? 'Kartu merah' : 'Kartu kuning' }} · Menit {{ $card->minute_label }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.matches.cards.destroy', [$match, $card]) }}" onsubmit="return confirm('Hapus kartu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada kartu.</p>
                            @endforelse
                        </div>
                        <form method="POST" action="{{ route('admin.matches.cards.store', $match) }}" class="mt-3 space-y-3 rounded-lg border border-blue-100 bg-blue-50/50 p-3">
                            @csrf
                            <input type="hidden" name="club_id" value="{{ $match->home_club_id }}">
                            <p class="text-xs text-slate-500">Pemain kartu diambil dari lineup aktif pertandingan. Pemain approved lain juga tersedia.</p>
                            <div>
                                <label class="label">Pemain</label>
                                <select name="player_id" class="input w-full" required>
                                    <option value="">Pilih pemain</option>
                                    @foreach ($homeSelectPlayers as $p)
                                        <option value="{{ $p['id'] }}">
                                            {{ $p['name'] }}
                                            @if ($p['in_lineup'])
                                                {{ $p['position_type'] === 'starter' ? '(Starter)' : '(Masuk dari cadangan)' }}
                                            @else
                                                (Pemain approved)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Jenis Kartu</label>
                                <select name="card_type" class="input w-full" required>
                                    <option value="yellow">Kartu kuning</option>
                                    <option value="red">Kartu merah</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Menit</label>
                                <input type="text" name="minute_label" class="input w-full" placeholder="24 atau 45+5" required pattern="[0-9]{1,3}(\+[0-9]{1,2})?" title="Gunakan format menit seperti 24 atau 45+5">
                            </div>
                            <button type="submit" class="btn-primary w-full">Tambah Kartu</button>
                        </form>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pergantian Pemain</p>
                            <span class="text-xs text-slate-500">{{ $homeSubs->count() }} pergantian</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            @forelse ($homeSubs as $substitution)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $substitution->playerOut?->name }} → {{ $substitution->playerIn?->name }}</p>
                                        <p class="text-xs text-slate-500">Menit {{ $substitution->minute_label }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.matches.substitutions.destroy', [$match, $substitution]) }}" onsubmit="return confirm('Hapus pergantian ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada pergantian.</p>
                            @endforelse
                        </div>
                        <form method="POST" action="{{ route('admin.matches.substitutions.store', $match) }}" class="mt-3 space-y-3 rounded-lg border border-blue-100 bg-blue-50/50 p-3">
                            @csrf
                            <input type="hidden" name="club_id" value="{{ $match->home_club_id }}">
                            <p class="text-xs text-slate-500">Pemain keluar diambil dari lineup aktif pertandingan, pemain masuk dari cadangan atau pemain approved lain.</p>
                            <div>
                                <label class="label">Pemain Keluar</label>
                                <select name="player_out_id" class="input w-full" required>
                                    <option value="">Pilih pemain keluar</option>
                                    @foreach ($homeActivePlayers as $slot)
                                        <option value="{{ $slot->player_id }}">{{ $slot->player?->name }} {{ $slot->position_type === 'starter' ? '(Starter)' : '(Masuk dari cadangan)' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Pemain Masuk</label>
                                <select name="player_in_id" class="input w-full" required>
                                    <option value="">Pilih pemain masuk</option>
                                    @foreach ($homeAvailableSubstitutes as $slot)
                                        <option value="{{ $slot->player_id }}">{{ $slot->player?->name }} (Cadangan)</option>
                                    @endforeach
                                    @foreach ($homeSelectPlayers as $p)
                                        @if (! $p['in_lineup'])
                                            <option value="{{ $p['id'] }}">{{ $p['name'] }} (Pemain approved)</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Menit</label>
                                <input type="text" name="minute_label" class="input w-full" placeholder="60" required pattern="[0-9]{1,3}(\+[0-9]{1,2})?" title="Gunakan format menit seperti 60 atau 90+2">
                            </div>
                            <button type="submit" class="btn-primary w-full">Tambah Pergantian</button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="card border-l-4 border-l-amber-600">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="panel-title">{{ $match->awayClub->name }}</h2>
                        <p class="panel-subtitle">Panel kanan untuk tim tamu.</p>
                    </div>
                    <span class="chip-soft">Away</span>
                </div>

                <div class="mt-6 space-y-6">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Gol</p>
                            <span class="text-xs text-slate-500">{{ $awayGoals->count() }} gol</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            @forelse ($awayGoals as $goal)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $goal->player?->name }}</p>
                                        <p class="text-xs text-slate-500">Menit {{ $goal->minute_label }}@if ($goal->is_penalty) · Penalti @endif</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.matches.goals.destroy', [$match, $goal]) }}" onsubmit="return confirm('Hapus gol ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada gol.</p>
                            @endforelse
                        </div>
                        <form method="POST" action="{{ route('admin.matches.goals.store', $match) }}" class="mt-3 space-y-3 rounded-lg border border-amber-100 bg-amber-50/50 p-3">
                            @csrf
                            <input type="hidden" name="club_id" value="{{ $match->away_club_id }}">
                            <p class="text-xs text-slate-500">Pemain diambil dari lineup yang sudah disusun klub admin. Pemain approved lain juga tersedia.</p>
                            <div>
                                <label class="label">Pemain</label>
                                <select name="player_id" class="input w-full" required>
                                    <option value="">Pilih pemain</option>
                                    @foreach ($awaySelectPlayers as $p)
                                        <option value="{{ $p['id'] }}">
                                            {{ $p['name'] }}
                                            @if ($p['in_lineup'])
                                                {{ $p['position_type'] === 'starter' ? '(Starter)' : '(Masuk dari cadangan)' }}
                                            @else
                                                (Pemain approved)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Menit</label>
                                <input type="text" name="minute_label" class="input w-full" placeholder="24 atau 45+5" required pattern="[0-9]{1,3}(\+[0-9]{1,2})?" title="Gunakan format menit seperti 24 atau 45+5">
                            </div>
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="is_penalty" value="1" class="rounded border-slate-300">
                                Penalti
                            </label>
                            <button type="submit" class="btn-primary w-full">Tambah Gol Away</button>
                        </form>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kartu</p>
                            <span class="text-xs text-slate-500">{{ $awayCards->count() }} kartu</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            @forelse ($awayCards as $card)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $card->player?->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $card->card_type === 'red' ? 'Kartu merah' : 'Kartu kuning' }} · Menit {{ $card->minute_label }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.matches.cards.destroy', [$match, $card]) }}" onsubmit="return confirm('Hapus kartu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada kartu.</p>
                            @endforelse
                        </div>
                        <form method="POST" action="{{ route('admin.matches.cards.store', $match) }}" class="mt-3 space-y-3 rounded-lg border border-amber-100 bg-amber-50/50 p-3">
                            @csrf
                            <input type="hidden" name="club_id" value="{{ $match->away_club_id }}">
                            <p class="text-xs text-slate-500">Pemain kartu diambil dari lineup aktif pertandingan. Pemain approved lain juga tersedia.</p>
                            <div>
                                <label class="label">Pemain</label>
                                <select name="player_id" class="input w-full" required>
                                    <option value="">Pilih pemain</option>
                                    @foreach ($awaySelectPlayers as $p)
                                        <option value="{{ $p['id'] }}">
                                            {{ $p['name'] }}
                                            @if ($p['in_lineup'])
                                                {{ $p['position_type'] === 'starter' ? '(Starter)' : '(Masuk dari cadangan)' }}
                                            @else
                                                (Pemain approved)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Jenis Kartu</label>
                                <select name="card_type" class="input w-full" required>
                                    <option value="yellow">Kartu kuning</option>
                                    <option value="red">Kartu merah</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Menit</label>
                                <input type="text" name="minute_label" class="input w-full" placeholder="24 atau 45+5" required pattern="[0-9]{1,3}(\+[0-9]{1,2})?" title="Gunakan format menit seperti 24 atau 45+5">
                            </div>
                            <button type="submit" class="btn-primary w-full">Tambah Kartu</button>
                        </form>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pergantian Pemain</p>
                            <span class="text-xs text-slate-500">{{ $awaySubs->count() }} pergantian</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            @forelse ($awaySubs as $substitution)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $substitution->playerOut?->name }} → {{ $substitution->playerIn?->name }}</p>
                                        <p class="text-xs text-slate-500">Menit {{ $substitution->minute_label }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.matches.substitutions.destroy', [$match, $substitution]) }}" onsubmit="return confirm('Hapus pergantian ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada pergantian.</p>
                            @endforelse
                        </div>
                        <form method="POST" action="{{ route('admin.matches.substitutions.store', $match) }}" class="mt-3 space-y-3 rounded-lg border border-amber-100 bg-amber-50/50 p-3">
                            @csrf
                            <input type="hidden" name="club_id" value="{{ $match->away_club_id }}">
                            <p class="text-xs text-slate-500">Pemain keluar diambil dari lineup aktif pertandingan, pemain masuk dari cadangan atau pemain approved lain.</p>
                            <div>
                                <label class="label">Pemain Keluar</label>
                                <select name="player_out_id" class="input w-full" required>
                                    <option value="">Pilih pemain keluar</option>
                                    @foreach ($awayActivePlayers as $slot)
                                        <option value="{{ $slot->player_id }}">{{ $slot->player?->name }} {{ $slot->position_type === 'starter' ? '(Starter)' : '(Masuk dari cadangan)' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Pemain Masuk</label>
                                <select name="player_in_id" class="input w-full" required>
                                    <option value="">Pilih pemain masuk</option>
                                    @foreach ($awayAvailableSubstitutes as $slot)
                                        <option value="{{ $slot->player_id }}">{{ $slot->player?->name }} (Cadangan)</option>
                                    @endforeach
                                    @foreach ($awaySelectPlayers as $p)
                                        @if (! $p['in_lineup'])
                                            <option value="{{ $p['id'] }}">{{ $p['name'] }} (Pemain approved)</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Menit</label>
                                <input type="text" name="minute_label" class="input w-full" placeholder="60" required pattern="[0-9]{1,3}(\+[0-9]{1,2})?" title="Gunakan format menit seperti 60 atau 90+2">
                            </div>
                            <button type="submit" class="btn-primary w-full">Tambah Pergantian</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        @if ($match->match_notes)
            <div class="card">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan Pertandingan</p>
                <p class="mt-2 whitespace-pre-wrap text-sm text-slate-700">{{ $match->match_notes }}</p>
            </div>
        @endif

        <div class="card bg-slate-50">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ringkasan Pertandingan</p>
            <form method="POST" action="{{ route('admin.matches.summary.update', $match) }}" class="mt-3 space-y-3">
                @csrf
                @method('PATCH')
                <textarea name="summary" class="input h-28 w-full" placeholder="Tuliskan ringkasan pertandingan, highlight, dan catatan penting...">{{ $match->summary }}</textarea>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="btn-primary">Simpan Summary</button>
                    @if ($match->summary)
                        <a href="{{ route('admin.matches.summary.download', $match) }}" class="btn-secondary">Cetak Summary</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>