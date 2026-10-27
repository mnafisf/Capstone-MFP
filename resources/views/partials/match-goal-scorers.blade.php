@props(['match'])

@php
    $goals = $match->relationLoaded('goals') ? $match->goals : $match->goals()->with('player')->get();
    $fmtPart = static function ($g): string {
        $m = $g->minute_label."'";

        return $g->is_penalty ? $m.' (P)' : $m;
    };
    $linesForSide = static function ($sideGoals) use ($fmtPart): \Illuminate\Support\Collection {
        return $sideGoals->groupBy('player_id')->map(function ($playerGoals) use ($fmtPart): string {
            $name = $playerGoals->first()->player?->name ?? '—';

            return $name.' '.$playerGoals->map($fmtPart)->implode(', ');
        })->values();
    };
    $homeLines = $linesForSide($goals->where('club_id', $match->home_club_id));
    $awayLines = $linesForSide($goals->where('club_id', $match->away_club_id));
@endphp

@if ($homeLines->isNotEmpty() || $awayLines->isNotEmpty())
    <div class="mt-2 grid grid-cols-[1fr_auto_1fr] gap-2 text-xs text-slate-600">
        <ul class="space-y-1 text-right">
            @foreach ($homeLines as $line)
                <li>{{ $line }}</li>
            @endforeach
        </ul>
        <span class="px-1 text-slate-400" aria-hidden="true">⚽</span>
        <ul class="space-y-1 text-left">
            @foreach ($awayLines as $line)
                <li>{{ $line }}</li>
            @endforeach
        </ul>
    </div>
@endif
