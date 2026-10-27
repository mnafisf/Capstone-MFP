<?php

namespace App\Repositories;

use App\Models\Player;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlayerRepository
{
    public function paginateForClub(int $clubId, ?string $search = null): LengthAwarePaginator
    {
        return Player::query()
            ->where('club_id', $clubId)
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): Player
    {
        return Player::create($data);
    }

    public function update(Player $player, array $data): Player
    {
        $player->update($data);

        return $player->refresh();
    }
}
