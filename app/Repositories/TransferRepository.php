<?php

namespace App\Repositories;

use App\Models\Transfer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransferRepository
{
    public function paginateForClub(int $clubId): LengthAwarePaginator
    {
        return Transfer::query()
            ->with(['player', 'fromClub', 'toClub'])
            ->where('from_club_id', $clubId)
            ->orWhere('to_club_id', $clubId)
            ->latest()
            ->paginate(10);
    }

    public function create(array $data): Transfer
    {
        return Transfer::create($data);
    }
}
