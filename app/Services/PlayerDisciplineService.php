<?php

namespace App\Services;

use App\Models\Event;
use App\Models\MatchCard;
use App\Models\MatchModel;
use Illuminate\Support\Collection;

class PlayerDisciplineService
{
    /**
     * @return array{
     *     is_suspended: bool,
     *     suspension_reason: string|null,
     *     yellow_count: int,
     *     yellow_threshold: int
     * }
     */
    public function getStatusForMatch(int $playerId, MatchModel $targetMatch, int $clubId): array
    {
        $targetMatch->loadMissing('event');
        $event = $targetMatch->event;

        if (! $event instanceof Event) {
            return $this->emptyStatus(3);
        }

        $clubMatches = $this->clubMatchesInEvent($event->id, $clubId);
        $targetIndex = $clubMatches->search(fn (MatchModel $match) => (int) $match->id === (int) $targetMatch->id);

        if ($targetIndex === false) {
            return $this->emptyStatus((int) $event->yellow_card_threshold);
        }

        $cardsByMatch = $this->playerCardsByMatch($playerId, $event->id, $clubMatches->pluck('id'));

        $yellowCount = 0;
        $yellowThreshold = (int) $event->yellow_card_threshold;
        $yellowBanMatches = (int) $event->yellow_card_ban_matches;
        $redBanMatches = (int) $event->red_card_ban_matches;
        $banUntilIndex = null;
        $banReason = null;
        $yellowBanIssued = false;

        for ($index = 0; $index < $targetIndex; $index++) {
            $match = $clubMatches[$index];
            $matchCards = $cardsByMatch->get($match->id, collect());

            foreach ($matchCards as $card) {
                if ($card->card_type === 'yellow') {
                    $yellowCount++;
                }

                if ($card->card_type === 'red') {
                    $candidateUntil = $index + $redBanMatches;
                    if ($banUntilIndex === null || $candidateUntil > $banUntilIndex) {
                        $banUntilIndex = $candidateUntil;
                        $banReason = 'kartu merah';
                    }
                }
            }

            if (! $yellowBanIssued && $yellowCount >= $yellowThreshold) {
                $candidateUntil = $index + $yellowBanMatches;
                if ($banUntilIndex === null || $candidateUntil > $banUntilIndex) {
                    $banUntilIndex = $candidateUntil;
                    $banReason = 'akumulasi kartu kuning';
                }

                $yellowBanIssued = true;
            }

            if ($banUntilIndex !== null && $index === $banUntilIndex) {
                $banUntilIndex = null;
                $banReason = null;
                $yellowCount = 0;
                $yellowBanIssued = false;
            }
        }

        $isSuspended = $banUntilIndex !== null && $targetIndex <= $banUntilIndex;

        return [
            'is_suspended' => $isSuspended,
            'suspension_reason' => $isSuspended ? $banReason : null,
            'yellow_count' => $yellowCount,
            'yellow_threshold' => $yellowThreshold,
        ];
    }

    /**
     * @param  Collection<int, MatchModel>  $clubMatches
     * @return Collection<int, Collection<int, MatchCard>>
     */
    private function playerCardsByMatch(int $playerId, int $eventId, Collection $matchIds): Collection
    {
        if ($matchIds->isEmpty()) {
            return collect();
        }

        return MatchCard::query()
            ->where('player_id', $playerId)
            ->whereIn('match_id', $matchIds)
            ->whereHas('match', fn ($query) => $query->where('event_id', $eventId))
            ->orderBy('id')
            ->get()
            ->groupBy('match_id');
    }

    /**
     * @return Collection<int, MatchModel>
     */
    private function clubMatchesInEvent(int $eventId, int $clubId): Collection
    {
        return MatchModel::query()
            ->where('event_id', $eventId)
            ->where(function ($query) use ($clubId): void {
                $query->where('home_club_id', $clubId)
                    ->orWhere('away_club_id', $clubId);
            })
            ->orderBy('match_date')
            ->orderBy('id')
            ->get()
            ->values();
    }

    /**
     * @return array{
     *     is_suspended: bool,
     *     suspension_reason: string|null,
     *     yellow_count: int,
     *     yellow_threshold: int
     * }
     */
    private function emptyStatus(int $yellowThreshold): array
    {
        return [
            'is_suspended' => false,
            'suspension_reason' => null,
            'yellow_count' => 0,
            'yellow_threshold' => $yellowThreshold,
        ];
    }
}
