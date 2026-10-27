<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\MatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicEventController extends Controller
{
    public function __construct(private readonly MatchService $matchService) {}

    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'semua');

        $eventsQuery = Event::query()->orderByDesc('start_date');

        if ($filter === 'berlangsung') {
            $eventsQuery->whereDate('start_date', '<=', now()->toDateString())
                ->whereDate('end_date', '>=', now()->toDateString());
        } elseif ($filter === 'mendatang') {
            $eventsQuery->whereDate('start_date', '>', now()->toDateString());
        } elseif ($filter === 'selesai') {
            $eventsQuery->whereDate('end_date', '<', now()->toDateString());
        } elseif ($filter === 'liga') {
            $eventsQuery->where('competition_type', 'league');
        } elseif ($filter === 'knockout') {
            $eventsQuery->where('competition_type', 'knockout');
        }

        $events = $eventsQuery->paginate(12);

        return view('public.events.index', [
            'events' => $events,
            'filter' => $filter,
        ]);
    }

    public function show(Event $event): View
    {
        $event->load([
            'matches' => fn ($query) => $query
                ->with([
                    'homeClub',
                    'awayClub',
                    'stadium',
                    'result',
                    'goals.player.club',
                    'cards.player.club',
                ])
                ->orderBy('match_date'),
            'bracketRounds' => [
                'matches' => fn ($query) => $query
                    ->with(['homeClub', 'awayClub', 'winnerClub'])
                    ->orderBy('position'),
            ],
        ]);

        $registrationClubs = EventRegistration::query()
            ->with('player.club')
            ->where('event_id', $event->id)
            ->get()
            ->map(fn ($registration) => $registration->player?->club)
            ->filter()
            ->unique('id');

        $matchClubs = $event->matches
            ->flatMap(fn ($match) => [$match->homeClub, $match->awayClub])
            ->filter()
            ->unique('id');

        $participantClubs = $registrationClubs
            ->merge($matchClubs)
            ->unique('id')
            ->sortBy('name')
            ->values();

        $standings = collect();
        if ($event->matches->isNotEmpty()) {
            $standings = $this->matchService->standingsByEvent($event->id);

            $standingsClubMap = Club::query()
                ->whereIn('id', $standings->pluck('club_id')->all())
                ->get(['id', 'name', 'code', 'shirt_color'])
                ->keyBy('id');

            $standings = $standings->map(function (array $row) use ($standingsClubMap): array {
                $club = $standingsClubMap[$row['club_id']] ?? null;

                return $row + [
                    'club_name' => $club?->name ?? 'Club #'.$row['club_id'],
                    'club_code' => $club?->code,
                    'shirt_color' => $club?->shirt_color,
                ];
            });
        }

        $clubMap = Club::query()->whereIn('id', $participantClubs->pluck('id'))->get()->keyBy('id');

        $topScorers = $this->matchService->topScorersByEvent($event->id);

        $yellowCards = DB::table('match_cards')
            ->join('players', 'players.id', '=', 'match_cards.player_id')
            ->join('clubs', 'clubs.id', '=', 'match_cards.club_id')
            ->join('matches', 'matches.id', '=', 'match_cards.match_id')
            ->where('matches.event_id', $event->id)
            ->where('match_cards.card_type', 'yellow')
            ->select(
                'players.id as player_id',
                'players.name as player_name',
                'clubs.id as club_id',
                'clubs.name as club_name',
                DB::raw('COUNT(*) as total_cards')
            )
            ->groupBy('players.id', 'players.name', 'clubs.id', 'clubs.name')
            ->orderByDesc('total_cards')
            ->limit(10)
            ->get();

        $redCards = DB::table('match_cards')
            ->join('players', 'players.id', '=', 'match_cards.player_id')
            ->join('clubs', 'clubs.id', '=', 'match_cards.club_id')
            ->join('matches', 'matches.id', '=', 'match_cards.match_id')
            ->where('matches.event_id', $event->id)
            ->where('match_cards.card_type', 'red')
            ->select(
                'players.id as player_id',
                'players.name as player_name',
                'clubs.id as club_id',
                'clubs.name as club_name',
                DB::raw('COUNT(*) as total_cards')
            )
            ->groupBy('players.id', 'players.name', 'clubs.id', 'clubs.name')
            ->orderByDesc('total_cards')
            ->limit(10)
            ->get();

        return view('public.events.show', [
            'event' => $event,
            'participantClubs' => $participantClubs,
            'clubMap' => $clubMap,
            'matches' => $event->matches,
            'standings' => $standings,
            'topScorers' => $topScorers,
            'yellowCards' => $yellowCards,
            'redCards' => $redCards,
        ]);
    }
}
