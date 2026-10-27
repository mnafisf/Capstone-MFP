<?php

namespace App\Services;

use App\Models\Club;
use App\Models\MatchModel;
use App\Models\MatchResult;
use App\Repositories\MatchRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MatchService
{
    public function __construct(
        private readonly MatchRepository $matchRepository,
        private readonly BracketService $bracketService
    ) {}

    public function create(array $data): MatchModel
    {
        return $this->matchRepository->create($data);
    }

    public function syncResultFromGoals(MatchModel $match): MatchResult
    {
        $homeScore = $match->goals()->where('club_id', $match->home_club_id)->count();
        $awayScore = $match->goals()->where('club_id', $match->away_club_id)->count();

        $result = MatchResult::updateOrCreate(
            ['match_id' => $match->id],
            ['home_score' => $homeScore, 'away_score' => $awayScore]
        );

        // Auto-update bracket winner if this match is linked to a bracket match
        // Only do this if match date has passed (to avoid live updates during match)
        // and result is not a draw (knockout matches need a winner)
        if ($match->event_bracket_match_id && $match->match_date && $match->match_date->isPast() && $homeScore !== $awayScore) {
            $this->updateBracketWinnerFromResult($match, $homeScore, $awayScore);
        }

        return $result;
    }

    private function updateBracketWinnerFromResult(MatchModel $match, int $homeScore, int $awayScore): void
    {
        $bracketMatch = $match->eventBracketMatch;

        if (! $bracketMatch || $bracketMatch->isDecided()) {
            return; // Already has winner or not a bracket match
        }

        $winnerClubId = $homeScore > $awayScore ? $match->home_club_id : $match->away_club_id;

        if (! $winnerClubId) {
            return;
        }

        $winnerClub = Club::find($winnerClubId);

        if (! $winnerClub) {
            return;
        }

        try {
            $this->bracketService->recordWinner($bracketMatch, $winnerClub);
        } catch (ValidationException $e) {
            // Ignore validation errors (e.g., winner not one of the teams)
            // Admin can manually set winner via bracket UI
        }
    }

    public function standingsByEvent(int $eventId): Collection
    {
        $matches = MatchModel::query()
            ->with('result')
            ->where('event_id', $eventId)
            ->whereHas('result')
            ->get();

        $table = collect();

        foreach ($matches as $match) {
            $home = $table->get($match->home_club_id, ['club_id' => $match->home_club_id, 'played' => 0, 'win' => 0, 'draw' => 0, 'loss' => 0, 'gf' => 0, 'ga' => 0, 'points' => 0]);
            $away = $table->get($match->away_club_id, ['club_id' => $match->away_club_id, 'played' => 0, 'win' => 0, 'draw' => 0, 'loss' => 0, 'gf' => 0, 'ga' => 0, 'points' => 0]);

            $home['played']++;
            $away['played']++;

            $home['gf'] += $match->result->home_score;
            $home['ga'] += $match->result->away_score;
            $away['gf'] += $match->result->away_score;
            $away['ga'] += $match->result->home_score;

            if ($match->result->home_score > $match->result->away_score) {
                $home['win']++;
                $home['points'] += 3;
                $away['loss']++;
            } elseif ($match->result->home_score < $match->result->away_score) {
                $away['win']++;
                $away['points'] += 3;
                $home['loss']++;
            } else {
                $home['draw']++;
                $away['draw']++;
                $home['points']++;
                $away['points']++;
            }

            $table->put($match->home_club_id, $home);
            $table->put($match->away_club_id, $away);
        }

        return $table->values()->sortByDesc(fn ($item) => [$item['points'], $item['gf'] - $item['ga'], $item['gf']])->values();
    }

    public function topScorersByEvent(int $eventId): Collection
    {
        return DB::table('match_player_stats')
            ->join('players', 'players.id', '=', 'match_player_stats.player_id')
            ->join('matches', 'matches.id', '=', 'match_player_stats.match_id')
            ->where('matches.event_id', $eventId)
            ->select('players.id', 'players.name', DB::raw('SUM(match_player_stats.goals) as total_goals'))
            ->groupBy('players.id', 'players.name')
            ->orderByDesc('total_goals')
            ->limit(10)
            ->get();
    }
}
