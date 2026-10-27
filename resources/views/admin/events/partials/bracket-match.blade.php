@props(['event', 'match', 'participants', 'slotEditable', 'usedElsewhere' => []])

@php
    $home = $match->homeClub?->name;
    $away = $match->awayClub?->name;
    $isWinnerHome = $match->winner_club_id !== null && $match->winner_club_id === $match->home_club_id;
    $isWinnerAway = $match->winner_club_id !== null && $match->winner_club_id === $match->away_club_id;
    $isTakenElsewhere = fn (int $clubId): bool => in_array($clubId, $usedElsewhere, true);
@endphp

<div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
    <p class="text-center text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
        Match {{ $match->position }}
    </p>

    @if ($slotEditable)
        <div class="mt-2 space-y-1.5">
            <select name="slots[{{ $match->id }}][home]" class="input text-xs">
                <option value="">-- Pilih tim --</option>
                @foreach ($participants as $participant)
                    <option
                        value="{{ $participant->club_id }}"
                        @selected((int) $match->home_club_id === (int) $participant->club_id)
                        @disabled($isTakenElsewhere((int) $participant->club_id))
                    >{{ $participant->club?->name ?? 'Klub #'.$participant->club_id }}</option>
                @endforeach
            </select>

            <select name="slots[{{ $match->id }}][away]" class="input text-xs">
                <option value="">-- Pilih tim --</option>
                @foreach ($participants as $participant)
                    <option
                        value="{{ $participant->club_id }}"
                        @selected((int) $match->away_club_id === (int) $participant->club_id)
                        @disabled($isTakenElsewhere((int) $participant->club_id))
                    >{{ $participant->club?->name ?? 'Klub #'.$participant->club_id }}</option>
                @endforeach
            </select>
        </div>
    @else
        <div class="mt-2 space-y-1">
            <div class="flex items-center justify-between gap-2 rounded-lg px-2 py-1 text-sm {{ $isWinnerHome ? 'bg-emerald-50 font-semibold text-emerald-800' : ($home ? 'text-slate-700' : 'text-slate-300') }}">
                <span class="truncate">{{ $home ?? 'Menunggu tim' }}</span>
                @if ($isWinnerHome)
                    <span class="text-xs">&#10003;</span>
                @endif
            </div>
            <div class="flex items-center justify-between gap-2 rounded-lg px-2 py-1 text-sm {{ $isWinnerAway ? 'bg-emerald-50 font-semibold text-emerald-800' : ($away ? 'text-slate-700' : 'text-slate-300') }}">
                <span class="truncate">{{ $away ?? ($home ? 'Bye' : 'Menunggu tim') }}</span>
                @if ($isWinnerAway)
                    <span class="text-xs">&#10003;</span>
                @endif
            </div>
        </div>
    @endif

    @if ($match->hasBothTeams() && ! $match->isDecided())
        @if ($match->scheduledMatch)
            <a href="{{ route('admin.matches.show', $match->scheduledMatch) }}" class="btn-secondary mt-2 w-full !py-1.5 !text-xs text-center block">Ke Pertandingan</a>
        @else
            <a href="{{ route('admin.events.bracket.schedule', [$event, $match]) }}" class="btn-primary mt-2 w-full !py-1.5 !text-xs text-center block">Buat Jadwal</a>
        @endif

        <div class="mt-2 border-t border-slate-100 pt-2">
            <select name="winner_club_id" form="winner-form-{{ $match->id }}" class="input text-xs" required>
                <option value="">Manual: Pilih pemenang</option>
                <option value="{{ $match->home_club_id }}">{{ $home }}</option>
                <option value="{{ $match->away_club_id }}">{{ $away }}</option>
            </select>
            <button form="winner-form-{{ $match->id }}" class="btn-ghost mt-1 w-full !py-1 !text-[10px]" type="submit">Catat Pemenang Manual</button>
        </div>
    @elseif (! $slotEditable)
        <p class="mt-2 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-400">
            {{ $match->statusLabel() }}
        </p>
    @endif
</div>
