<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Event;
use App\Models\EventBracketMatch;
use App\Models\EventBracketRound;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BracketService
{
    public const MIN_TEAMS = 2;

    public const MAX_TEAMS = 64;

    /**
     * Buat bagan knockout untuk sebuah event.
     *
     * Jumlah tim dibulatkan ke powers of two, sisa tim kosong menjadi bye
     * (slot kosong di babak pertama).
     */
    public function generate(Event $event, int $teamCount): Event
    {
        if ($teamCount < self::MIN_TEAMS) {
            throw ValidationException::withMessages([
                'team_count' => 'Minimal tim yang mengikuti adalah '.self::MIN_TEAMS.' klub.',
            ]);
        }

        if ($teamCount > self::MAX_TEAMS) {
            throw ValidationException::withMessages([
                'team_count' => 'Maksimal tim yang mengikuti adalah '.self::MAX_TEAMS.' klub.',
            ]);
        }

        $size = $this->bracketSize($teamCount);
        $totalRounds = (int) log($size, 2);

        DB::transaction(function () use ($event, $size, $totalRounds): void {
            $this->reset($event);

            $rounds = [];

            for ($roundNumber = 1; $roundNumber <= $totalRounds; $roundNumber++) {
                $matchCount = (int) ($size / (2 ** $roundNumber));

                $rounds[$roundNumber] = EventBracketRound::create([
                    'event_id' => $event->id,
                    'round_number' => $roundNumber,
                    'name' => $this->roundName($size, $roundNumber),
                    'match_count' => $matchCount,
                ]);
            }

            $matchesByRound = [];

            for ($roundNumber = 1; $roundNumber <= $totalRounds; $roundNumber++) {
                $matchesByRound[$roundNumber] = [];

                for ($position = 1; $position <= $rounds[$roundNumber]->match_count; $position++) {
                    $matchesByRound[$roundNumber][$position] = EventBracketMatch::create([
                        'event_bracket_round_id' => $rounds[$roundNumber]->id,
                        'position' => $position,
                    ]);
                }
            }

            for ($roundNumber = 1; $roundNumber < $totalRounds; $roundNumber++) {
                $nextMatches = $matchesByRound[$roundNumber + 1];

                foreach ($matchesByRound[$roundNumber] as $position => $match) {
                    $nextPosition = intdiv($position - 1, 2) + 1;
                    $target = $nextMatches[$nextPosition];

                    $match->update([
                        'winner_to_match_id' => $target->id,
                        'winner_to_position' => $position % 2 === 1 ? 1 : 2,
                    ]);
                }
            }
        });

        return $event->refresh();
    }

    /**
     * Isi slot babak pertama sesuai pilihan panitia.
     *
     * Penempatan sepenuhnya manual: tidak ada undian. Slot yang diisi melepaui
     * capacities bracket sehingga admin bebas menentukan pasangan tim.
     *
     * @param  array<int, array{home?: int|null, away?: int|null}>  $slots  diindex dengan id match babak pertama
     */
    public function assignFirstRoundSlots(Event $event, array $slots): void
    {
        DB::transaction(function () use ($event, $slots): void {
            $firstRound = $event->bracketRounds()->where('round_number', 1)->firstOrFail();

            foreach ($firstRound->matches as $match) {
                $entry = $slots[$match->id] ?? [];

                $match->update([
                    'home_club_id' => isset($entry['home']) ? (int) $entry['home'] : null,
                    'away_club_id' => isset($entry['away']) ? (int) $entry['away'] : null,
                    'winner_club_id' => null,
                ]);
            }

            $this->clearLaterRounds($event);
        });
    }

    /**
     * Kosongkan hasil babak berikutnya supaya tidak bentrok dengan susunan baru.
     */
    public function clearLaterRounds(Event $event): void
    {
        $laterMatches = $event->bracketRounds()
            ->where('round_number', '>', 1)
            ->with('matches')
            ->get()
            ->flatMap(fn (EventBracketRound $round) => $round->matches->pluck('id'));

        if ($laterMatches->isEmpty()) {
            return;
        }

        EventBracketMatch::query()
            ->whereIn('id', $laterMatches)
            ->update([
                'home_club_id' => null,
                'away_club_id' => null,
                'winner_club_id' => null,
            ]);
    }

    /**
     * Catat pemenang satu pertandingan bagan dan teruskan ke babak berikutnya.
     */
    public function recordWinner(EventBracketMatch $match, Club $winner): EventBracketMatch
    {
        if (! $match->hasBothTeams()) {
            throw ValidationException::withMessages([
                'winner' => 'Pertandingan ini belum memiliki dua tim.',
            ]);
        }

        if (! in_array($winner->id, [(int) $match->home_club_id, (int) $match->away_club_id], true)) {
            throw ValidationException::withMessages([
                'winner' => 'Pemenang harus salah satu klub yang tampil di pertandingan ini.',
            ]);
        }

        DB::transaction(function () use ($match, $winner): void {
            $match->update(['winner_club_id' => $winner->id]);

            $target = $match->winnerToMatch()->first();

            if ($target && $match->winner_to_position) {
                $target->update([
                    $match->winner_to_position === 1 ? 'home_club_id' : 'away_club_id' => $winner->id,
                    'winner_club_id' => null,
                ]);
            }
        });

        return $match->refresh();
    }

    /**
     * Hapus bagan (rounds dan matches). Daftar peserta tetap disimpan.
     */
    public function reset(Event $event): void
    {
        EventBracketMatch::query()
            ->whereIn('event_bracket_round_id', $event->bracketRounds()->select('id'))
            ->delete();

        $event->bracketRounds()->delete();

        $event->participants()->update(['seed' => null]);
    }

    public function bracketSize(int $teamCount): int
    {
        $size = self::MIN_TEAMS;

        while ($size < $teamCount && $size < self::MAX_TEAMS) {
            $size *= 2;
        }

        return $size;
    }

    public function roundName(int $size, int $roundNumber): string
    {
        $teamsInRound = (int) ($size / (2 ** ($roundNumber - 1)));

        return match ($teamsInRound) {
            2 => 'Final',
            4 => 'Semifinal',
            8 => 'Perempat Final',
            16 => 'Babak 16 Besar',
            32 => 'Babak 32 Besar',
            64 => 'Babak 64 Besar',
            default => 'Babak '.$teamsInRound,
        };
    }

    /**
     * Kapasitas slot babak pertama pada bagan yang sudah dibuat.
     */
    public function bracketCapacity(Event $event): ?int
    {
        $firstRound = $event->bracketRounds()->where('round_number', 1)->first();

        return $firstRound ? $firstRound->match_count * 2 : null;
    }

    /**
     * Daftar klub aktif yang belum jadi peserta event ini.
     *
     * @return Collection<int, Club>
     */
    public function availableClubs(Event $event): Collection
    {
        return Club::query()
            ->where('is_active', true)
            ->whereNotIn('id', $event->participants()->select('club_id'))
            ->orderBy('name')
            ->get();
    }
}
