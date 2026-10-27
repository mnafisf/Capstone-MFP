<x-app-layout>
    <x-slot name="header">
        <div class="super-hero-vibrant">
            <div class="relative z-10">
                <span class="chip-soft border-white/30 bg-white/15 text-white">Panel Admin Klub</span>
                <h1 class="mt-3 text-3xl font-bold tracking-tight">Input Lineup Pertandingan</h1>
                <p class="mt-1 text-sm text-blue-100">Susun pemain utama dan cadangan dengan centang pemain. Ubah atau hapus selama pertandingan belum dimulai.</p>
            </div>
        </div>
    </x-slot>

    @php
        $queryMatchId = (int) request('match_id', 0);
        $activeMatchId = $queryMatchId > 0 
            ? $queryMatchId 
            : (int) old('match_id', $editingLineup?->match_id ?? $matchesForForm->first()?->id);
    @endphp

    @error('lineup')
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
    @enderror

    <div class="space-y-6">
        <div class="surface-red-blue">
            @if ($editingLineup && ! $editingLineup->match->canManageLineups())
                <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                    Deadline lineup untuk pertandingan ini sudah berakhir, jadi susunan pemain tidak bisa diubah.
                </p>
            @elseif ($players->isEmpty())
                <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                    Belum ada pemain disetujui. Tambahkan pemain di menu pemain terlebih dahulu.
                </p>
            @elseif (! $editingLineup && $matchesForForm->isEmpty())
                <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                    Tidak ada pertandingan dengan deadline lineup aktif untuk klub Anda.
                </p>
            @else
                <form method="POST" action="{{ route('club-admin.lineups.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label">Pertandingan</label>
                        @if ($editingLineup)
                            <input type="hidden" name="match_id" value="{{ $editingLineup->match_id }}">
                            <p class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800">
                                {{ $editingLineup->match->homeClub->name }} vs {{ $editingLineup->match->awayClub->name }}
                                <span class="text-slate-500">({{ $editingLineup->match->match_date->format('d M Y H:i') }})</span>
                                @if ($editingLineup->match->lineup_deadline)
                                    <span class="ml-2 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">Deadline {{ $editingLineup->match->lineup_deadline->format('d M Y H:i') }}</span>
                                @endif
                            </p>
                            <a href="{{ route('club-admin.lineups.index') }}" class="mt-2 inline-block text-xs font-semibold text-blue-700 hover:underline">Batal edit</a>
                        @else
                            <select name="match_id" id="match_id" class="input" required>
                                @foreach ($matchesForForm as $match)
                                    <option value="{{ $match->id }}" @selected((int) old('match_id', $activeMatchId) === (int) $match->id)>
                                        {{ $match->homeClub->name }} vs {{ $match->awayClub->name }} ({{ $match->match_date->format('d M Y H:i') }}) - Deadline {{ $match->lineup_deadline->format('d M Y H:i') }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    @error('match_id')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('deadline')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('starters')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('substitutes')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">No Punggung</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Nama</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Posisi</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">C</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Akumulasi</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($players as $player)
                                    @php
                                        $playerDiscipline = $disciplineByMatch[$activeMatchId][$player->id] ?? null;
                                        $isSuspended = $playerDiscipline['is_suspended'] ?? false;
                                        $selectedStatus = old('player_statuses.'.$player->id, $selectedPlayerStatuses[$player->id] ?? '');
                                        $selectedPosition = old('player_positions.'.$player->id, $selectedPlayerPositions[$player->id] ?? '');
                                        $isCaptain = old('player_captains.'.$player->id, $selectedPlayerCaptains[$player->id] ?? false);
                                    @endphp
                                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3 text-slate-800 font-semibold">
                                            {{ $player->jersey_number ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-800">
                                            <span class="font-medium">{{ $player->name }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select name="player_statuses[{{ $player->id }}]" class="input w-32" @disabled($isSuspended)>
                                                <option value="">Tidak bermain</option>
                                                <option value="starter" @selected($selectedStatus === 'starter')>Pemain Utama</option>
                                                <option value="substitute" @selected($selectedStatus === 'substitute')>Cadangan</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select name="player_positions[{{ $player->id }}]" class="input w-24" @disabled(!$selectedStatus || $isSuspended)>
                                                <option value="">Posisi</option>
                                                @foreach ($positionOptions as $positionOption)
                                                    <option value="{{ $positionOption }}" @selected($selectedPosition === $positionOption)>
                                                        {{ $positionOption }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" name="player_captains[{{ $player->id }}]" value="1" @checked($isCaptain) class="rounded border-slate-300 text-blue-600" @disabled($isSuspended || !$selectedStatus)>
                                        </td>
                                        <td class="px-4 py-3 text-slate-800">
                                            <span class="text-xs {{ (! $isSuspended && ($playerDiscipline['yellow_count'] ?? 0) > 0) ? 'text-amber-700 font-semibold' : 'text-slate-500' }}">
                                                {{ (! $isSuspended && ($playerDiscipline['yellow_count'] ?? 0) > 0) ? ($playerDiscipline['yellow_count'] ?? 0) . '/' . ($playerDiscipline['yellow_threshold'] ?? 0) : '—' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($isSuspended)
                                                <span class="inline-block rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                    Larangan {{ $playerDiscipline['suspension_reason'] ?? '' }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-500">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button class="btn-primary" type="submit">{{ $editingLineup ? 'Perbarui susunan pemain' : 'Simpan susunan pemain' }}</button>
                </form>
            @endif
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="px-4 py-3">Pertandingan</th>
                            <th class="px-4 py-3">Pemain Utama</th>
                            <th class="px-4 py-3">Cadangan</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lineups as $lineup)
                            @php($canEditLineup = $lineup->match->canManageLineups())
                            <tr class="table-row-hover border-t align-top transition-colors hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <div class="space-y-1.5">
                                        <p class="text-sm font-semibold text-slate-900">{{ $lineup->match->homeClub->name }} vs {{ $lineup->match->awayClub->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $lineup->match->match_date->format('d M Y H:i') }}</p>
                                        <p class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            Deadline lineup: {{ $lineup->match->lineup_deadline?->format('d M Y H:i') ?? '-' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($lineup->lineupPlayers->where('position_type', 'starter') as $starter)
                                            <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                                <span class="font-semibold">{{ $starter->field_position ?: 'ST' }}</span>
                                                <span>{{ $starter->player?->name }}</span>
                                            </span>
                                        @empty
                                            <span class="text-sm text-slate-400">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($lineup->lineupPlayers->where('position_type', 'substitute') as $substitute)
                                            <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                <span class="font-semibold">{{ $substitute->field_position ?: 'SUB' }}</span>
                                                <span>{{ $substitute->player?->name }}</span>
                                            </span>
                                        @empty
                                            <span class="text-sm text-slate-400">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-wrap gap-3 text-sm font-semibold whitespace-nowrap">
                                        <a href="{{ route('club-admin.lineups.dsp.download', $lineup) }}" class="text-emerald-700 hover:underline">Unduh DSP PDF</a>
                                        @if ($canEditLineup)
                                            <a href="{{ route('club-admin.lineups.index', ['edit' => $lineup->id]) }}" class="text-blue-700 hover:underline">Ubah</a>
                                            <form method="POST" action="{{ route('club-admin.lineups.destroy', $lineup) }}" onsubmit="return confirm('Hapus line up untuk pertandingan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada susunan pemain tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $lineups->links() }}</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const matchSelect = document.getElementById('match_id');
            const statusSelects = document.querySelectorAll('select[name^="player_statuses"]');
            const positionSelects = document.querySelectorAll('select[name^="player_positions"]');
            const captainCheckboxes = document.querySelectorAll('input[name^="player_captains"]');

            const updateFormState = () => {
                statusSelects.forEach((statusSelect) => {
                    const playerId = statusSelect.name.match(/\[(\d+)\]/)[1];
                    const positionSelect = document.querySelector(`select[name="player_positions[${playerId}]"]`);
                    const captainCheckbox = document.querySelector(`input[name="player_captains[${playerId}]"]`);

                    const status = statusSelect.value;
                    positionSelect.disabled = !status;
                    captainCheckbox.disabled = !status;
                });
            };

            statusSelects.forEach((select) => {
                select.addEventListener('change', updateFormState);
            });

            if (matchSelect) {
                matchSelect.addEventListener('change', () => {
                    const url = new URL(window.location);
                    url.searchParams.set('match_id', matchSelect.value);
                    url.searchParams.delete('edit');
                    window.location.href = url.toString();
                });
            }

            updateFormState();
        });
    </script>
</x-app-layout>


    
