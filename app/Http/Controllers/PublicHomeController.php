<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\MatchModel;
use App\Models\Player;
use App\Services\MatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicHomeController extends Controller
{
    public function __construct(private readonly MatchService $matchService)
    {
    }

    public function __invoke(Request $request): View
    {
        $selectedEventId = $request->integer('event_id') ?: null;
        $clubQuery = trim((string) $request->query('club', ''));

        if (! $this->isCompetitionSchemaReady()) {
            return view('home', [
                'featuredEvent' => null,
                'availableEvents' => collect(),
                'selectedEventId' => $selectedEventId,
                'clubQuery' => $clubQuery,
                'upcomingMatches' => collect(),
                'recentResults' => collect(),
                'standings' => collect(),
                'topScorers' => collect(),
                'formGuide' => [],
                'totalClubs' => 0,
                'totalPlayers' => 0,
                'totalMatches' => 0,
                'totalEvents' => 0,
                'clubs' => collect(),
            ]);
        }

        $availableEvents = Event::query()
            ->orderByDesc('start_date')
            ->limit(20)
            ->get();

        $featuredEvent = Event::query()
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderByDesc('start_date')
            ->first();

        if (! $featuredEvent) {
            $featuredEvent = Event::query()->orderByDesc('start_date')->first();
        }

        $effectiveEventId = $selectedEventId ?: $featuredEvent?->id;

        $upcomingMatchesQuery = MatchModel::query()
            ->with(['homeClub', 'awayClub', 'stadium'])
            ->where('match_date', '>=', now())
            ->orderBy('match_date');

        $recentResultsQuery = MatchModel::query()
            ->with(['homeClub', 'awayClub', 'result', 'stadium', 'goals.player'])
            ->whereHas('result')
            ->orderByDesc('match_date');

        if ($effectiveEventId) {
            $upcomingMatchesQuery->where('event_id', $effectiveEventId);
            $recentResultsQuery->where('event_id', $effectiveEventId);
        }

        if ($clubQuery !== '') {
            $upcomingMatchesQuery->where(function ($query) use ($clubQuery): void {
                $query->whereHas('homeClub', fn ($club) => $club->where('name', 'like', '%'.$clubQuery.'%'))
                    ->orWhereHas('awayClub', fn ($club) => $club->where('name', 'like', '%'.$clubQuery.'%'));
            });

            $recentResultsQuery->where(function ($query) use ($clubQuery): void {
                $query->whereHas('homeClub', fn ($club) => $club->where('name', 'like', '%'.$clubQuery.'%'))
                    ->orWhereHas('awayClub', fn ($club) => $club->where('name', 'like', '%'.$clubQuery.'%'));
            });
        }

        $upcomingMatches = $upcomingMatchesQuery->limit(6)->get();
        $recentResults = $recentResultsQuery->limit(6)->get();

        $standings = collect();
        $topScorers = collect();
        $formGuide = [];

        if ($effectiveEventId) {
            $standings = $this->matchService->standingsByEvent($effectiveEventId);

            $clubIds = $standings->pluck('club_id')->all();
            $clubMap = Club::query()
                ->whereIn('id', $clubIds)
                ->get(['id', 'name', 'code', 'shirt_color'])
                ->keyBy('id');

            $standings = $standings->map(function (array $row) use ($clubMap): array {
                $club = $clubMap[$row['club_id']] ?? null;

                return $row + [
                    'club_name' => $club?->name ?? 'Club #'.$row['club_id'],
                    'club_code' => $club?->code,
                    'shirt_color' => $club?->shirt_color,
                ];
            });

            $topScorers = $this->matchService->topScorersByEvent($effectiveEventId);
            $formGuide = $this->formGuideByEvent($effectiveEventId);
        }

        $clubsQuery = Club::query()
            ->where('is_active', true)
            ->withCount(['players', 'officials'])
            ->orderBy('name');

        if ($clubQuery !== '') {
            $clubsQuery->where(function ($query) use ($clubQuery): void {
                $query->where('name', 'like', '%'.$clubQuery.'%')
                    ->orWhere('code', 'like', '%'.$clubQuery.'%');
            });
        }

        $clubs = $clubsQuery->limit(8)->get();

        return view('home', [
            'featuredEvent' => $featuredEvent,
            'availableEvents' => $availableEvents,
            'selectedEventId' => $selectedEventId,
            'clubQuery' => $clubQuery,
            'upcomingMatches' => $upcomingMatches,
            'recentResults' => $recentResults,
            'standings' => $standings,
            'topScorers' => $topScorers,
            'formGuide' => $formGuide,
            'totalClubs' => Club::count(),
            'totalPlayers' => Player::count(),
            'totalMatches' => MatchModel::count(),
            'totalEvents' => Event::count(),
            'clubs' => $clubs,
        ]);
    }

    /**
     * Form guide (5 terakhir) per klub: M = menang, S = seri, K = kalah.
     *
     * @return array<int, list<string>>
     */
    private function formGuideByEvent(?int $eventId): array
    {
        if (! $eventId) {
            return [];
        }

        $finishedMatches = MatchModel::query()
            ->with('result')
            ->where('event_id', $eventId)
            ->whereHas('result')
            ->orderByDesc('match_date')
            ->get(['id', 'home_club_id', 'away_club_id']);

        $guide = [];

        foreach ($finishedMatches as $match) {
            $result = $match->result;

            if (! $result) {
                continue;
            }

            if ($result->home_score > $result->away_score) {
                $homeOutcome = 'M';
                $awayOutcome = 'K';
            } elseif ($result->home_score < $result->away_score) {
                $homeOutcome = 'K';
                $awayOutcome = 'M';
            } else {
                $homeOutcome = 'S';
                $awayOutcome = 'S';
            }

            $guide[(int) $match->home_club_id][] = $homeOutcome;
            $guide[(int) $match->away_club_id][] = $awayOutcome;
        }

        return array_map(
            static fn (array $outcomes): array => array_slice($outcomes, 0, 5),
            $guide,
        );
    }

    private function isCompetitionSchemaReady(): bool
    {
        return Schema::hasTable('events')
            && Schema::hasTable('clubs')
            && Schema::hasTable('players')
            && Schema::hasTable('matches')
            && Schema::hasTable('match_player_stats')
            && Schema::hasTable('match_results')
            && Schema::hasTable('match_goals');
    }
}
