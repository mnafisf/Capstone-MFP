<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Player;
use App\Repositories\PlayerRepository;
use Illuminate\Validation\ValidationException;

class PlayerService
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly CompetitionSettingService $settingService
    ) {
    }

    public function createForClub(int $clubId, array $data): Player
    {
        $data['club_id'] = $clubId;

        return $this->playerRepository->create($data);
    }

    public function update(Player $player, array $data): Player
    {
        return $this->playerRepository->update($player, $data);
    }

    public function validateEventAge(Event $event, Player $player): void
    {
        if (! $event->isAgeAllowed($player->age)) {
            throw ValidationException::withMessages([
                'player_id' => 'Usia pemain tidak sesuai kategori umur event.',
            ]);
        }
    }

}
