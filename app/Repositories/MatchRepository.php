<?php

namespace App\Repositories;

use App\Models\MatchModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MatchRepository
{

    public function paginate(
        ?string $search = null,
        ?int $eventId = null
    ): LengthAwarePaginator {
        $numberedMatches = MatchModel::query()
            ->select('id')
            ->selectRaw(
                'ROW_NUMBER() OVER (
                    PARTITION BY event_id
                    ORDER BY match_date ASC, id ASC
                ) AS event_match_number'
            );

        return MatchModel::query()
            ->leftJoinSub($numberedMatches, 'numbered_matches', function ($join) {
                $join->on('matches.id', '=', 'numbered_matches.id');
            })
            ->select('matches.*')
            ->selectRaw('numbered_matches.event_match_number AS event_match_number')
            ->with([
                'event',
                'homeClub',
                'awayClub',
                'stadium',
                'referee',
                'referee2',
                'referee3',
                'result',
                'goals.player',
            ])
            ->when($eventId !== null, function ($query) use ($eventId) {
                $query->where('matches.event_id', $eventId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('homeClub', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('awayClub', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('matches.match_date')
            ->orderBy('matches.id')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): MatchModel
    {
        return MatchModel::create($data);
    }
}
