@php
    $isKnockout = $event->isKnockout();
    $participantIds = $event->participants->pluck('club_id')->all();
    $hasBracket = $event->bracketRounds->isNotEmpty();
    $firstRound = $event->bracketRounds->firstWhere('round_number', 1);
    $bracketCapacity = $firstRound ? $firstRound->match_count * 2 : null;
    $totalSlots = $event->bracketRounds->sum('match_count');
    $slotEditable = $isKnockout && $hasBracket && $event->participants->isNotEmpty();

    // Klub yang sudah dipakai di slot babak pertama lain -> disable di select.
    $slotUsage = $firstRound
        ? $firstRound->matches->mapWithKeys(fn ($match): array => [
            $match->id => array_values(array_filter([$match->home_club_id, $match->away_club_id])),
        ])
        : collect();
    $allUsedClubIds = $slotUsage->flatten()->unique()->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">{{ $isKnockout ? 'Knockout' : 'Liga' }}</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Pilih Tim - {{ $event->name }}</h1>
                <p class="mt-1 text-sm text-blue-100">
                    {{ $isKnockout
                        ? 'Buat bagan knockout, lalu tentukan sendiri tim yang mengisi setiap slot babak pertama.'
                        : 'Tentukan klub mana saja yang akan mengikuti event ini.' }}
                </p>
            </div>
            <div class="super-stat-grid">
                <div class="super-stat-card-strong">
                    <p class="super-stat-label">Tim Terpilih</p>
                    <p class="super-stat-value">{{ count($participantIds) }}</p>
                </div>
                @if ($isKnockout)
                    <div class="super-stat-card-strong">
                        <p class="super-stat-label">Slot Bagan</p>
                        <p class="super-stat-value">{{ $bracketCapacity ?? '-' }}</p>
                    </div>
                    <div class="super-stat-card-strong">
                        <p class="super-stat-label">Total Pertandingan</p>
                        <p class="super-stat-value">{{ $totalSlots }}</p>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="card">
            <div class="toolbar">
                <div class="min-w-0">
                    <h2 class="panel-title">Daftar Tim Peserta</h2>
                    <p class="panel-subtitle">
                        @if ($isKnockout && $hasBracket && count($participantIds) > 0)
                            Pilih tim yang akan mengikuti, lalu tentukan sendiri pasangan pada setiap match di bawah.
                        @else
                            Centang klub yang akan mengikuti event ini.
                        @endif
                    </p>
                </div>
                <div class="flex shrink-0 flex-wrap gap-2">
                    <a href="{{ route('admin.events.index') }}" class="btn-ghost">Kembali ke Daftar Event</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.events.participants.store', $event) }}">
                @csrf

                <div class="mt-4 grid gap-6 lg:grid-cols-2">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Sudah Terpilih ({{ count($participantIds) }})</p>

                        <div class="mt-2 space-y-2">
                            @forelse ($event->participants as $participant)
                                <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                    <input type="checkbox" name="club_ids[]" value="{{ $participant->club_id }}" checked class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-slate-800">{{ $participant->club?->name ?? 'Klub #'.$participant->club_id }}</span>
                                        <span class="block text-xs text-slate-500">Kode: {{ $participant->club?->code ?? '-' }}</span>
                                    </span>
                                    @if ($participant->seed !== null)
                                        <span class="status-badge status-active shrink-0">Urutan {{ $participant->seed }}</span>
                                    @endif
                                </label>
                            @empty
                                <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-4 text-center text-sm text-slate-500">
                                    Belum ada tim yang dipilih.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Kandidat Klub ({{ $availableClubs->count() }})</p>

                        <div class="mt-2 max-h-96 space-y-2 overflow-y-auto pr-1">
                            @forelse ($availableClubs as $club)
                                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2 transition hover:border-red-300 hover:bg-red-50/40">
                                    <input type="checkbox" name="club_ids[]" value="{{ $club->id }}" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-slate-800">{{ $club->name }}</span>
                                        <span class="block text-xs text-slate-500">Kode: {{ $club->code ?? '-' }}</span>
                                    </span>
                                </label>
                            @empty
                                <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-4 text-center text-sm text-slate-500">
                                    Semua klub aktif sudah dipilih.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="action-frame mt-6 justify-end">
                    <button class="btn-primary" type="submit">Simpan Pilihan Tim</button>
                </div>
            </form>
        </div>

        @if ($isKnockout)
            <div class="card">
                <div class="toolbar">
                    <div class="min-w-0">
                        <h2 class="panel-title">Bagan Knockout</h2>
                        <p class="panel-subtitle">
                            @if ($hasBracket)
                                @if ($slotEditable)
                                    Tentukan sendiri tim yang mengisi tiap slot babak pertama, lalu catat pemenang agar tim maju.
                                @else
                                    Bagan sudah dibuat untuk {{ count($participantIds) }} slot. Pilih tim peserta dulu agar slot bisa diisi.
                                @endif
                            @else
                                Tentukan berapa tim yang akan mengikuti, lalu bagan dibuat otomatis (jumlah tim dibulatkan ke kelipatan 2).
                            @endif
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        @if ($hasBracket)
                            <form method="POST" action="{{ route('admin.events.bracket.destroy', $event) }}" onsubmit="return confirm('Hapus bagan ini? Klub peserta tetap tersimpan.');">
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger" type="submit">Hapus Bagan</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.events.bracket.generate', $event) }}" class="flex flex-wrap items-end gap-2">
                                @csrf
                                <div>
                                    <label for="team_count" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Tim</label>
                                    <select name="team_count" id="team_count" class="input mt-1">
                                        @foreach ($bracketSizeOptions as $size)
                                            <option value="{{ $size }}">{{ $size }} tim</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn-primary" type="submit">Buat Bagan Otomatis</button>
                            </form>
                        @endif
                    </div>
                </div>

                @if ($hasBracket)
                    @if ($slotEditable)
                        <form method="POST" action="{{ route('admin.events.bracket.slots', $event) }}">
                            @csrf
                            @method('PATCH')
                    @endif

                    <div class="mt-6 overflow-x-auto pb-2">
                        <div class="flex min-w-max gap-6">
                            @foreach ($event->bracketRounds as $round)
                                @php
                                    $isFirstRound = $round->round_number === 1;
                                @endphp
                                <div class="flex w-64 shrink-0 flex-col">
                                    <p class="border-b-2 border-red-600 pb-2 text-center text-xs font-bold uppercase tracking-wide text-slate-700">
                                        {{ $round->name }}
                                    </p>
                                    <div class="mt-3 flex flex-1 flex-col justify-around gap-3">
                                        @foreach ($round->matches as $match)
                                            @php
                                                $usedElsewhere = $isFirstRound && $slotEditable
                                                    ? $allUsedClubIds->diff($slotUsage->get($match->id, []))->values()->all()
                                                    : [];
                                            @endphp
                                            @include('admin.events.partials.bracket-match', [
                                                'event' => $event,
                                                'match' => $match,
                                                'participants' => $event->participants,
                                                'slotEditable' => $slotEditable && $isFirstRound,
                                                'usedElsewhere' => $usedElsewhere,
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($slotEditable)
                        <div class="mt-4 flex justify-end border-t border-slate-200 pt-4">
                            <button class="btn-primary" type="submit">Simpan Semua Tim pada Bagan</button>
                        </div>
                        </form>
                    @endif

                    @foreach ($event->bracketRounds->flatMap->matches as $m)
                        @if ($m->hasBothTeams() && ! $m->isDecided())
                            <form id="winner-form-{{ $m->id }}" method="POST" action="{{ route('admin.events.bracket.winner', [$event, $m]) }}" class="hidden">
                                @csrf
                                @method('PATCH')
                            </form>
                        @endif
                    @endforeach
                @else
                    <p class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                        Bagan belum dibuat. Tentukan jumlah tim di atas untuk membuat bagan otomatis.
                    </p>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
