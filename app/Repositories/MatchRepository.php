<?php

namespace App\Repositories;

use App\Models\MatchModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MatchRepository
{
    public function paginate(?string $search = null): LengthAwarePaginator
    {
        return MatchModel::query()
            ->with(['event', 'homeClub', 'awayClub', 'stadium', 'referee', 'referee2', 'referee3', 'result', 'goals.player'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('homeClub', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('awayClub', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->latest('match_date')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): MatchModel
    {
        return MatchModel::create($data);
    }
}
