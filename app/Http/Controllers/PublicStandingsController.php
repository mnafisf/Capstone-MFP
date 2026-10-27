<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Services\MatchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicStandingsController extends Controller
{
    public function __construct(private readonly MatchService $matchService) {}

    public function index(Request $request): View
    {
        $eventId = $request->integer('event_id') ?: null;

        $availableEvents = Event::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->get();

        if (! $eventId && $availableEvents->isNotEmpty()) {
            $eventId = $availableEvents->first()->id;
        }

        $standings = collect();
        $topScorers = collect();
        $selectedEvent = null;

        if ($eventId) {
            $selectedEvent = Event::find($eventId);
            $standings = $this->matchService->standingsByEvent($eventId);

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

            $topScorers = $this->matchService->topScorersByEvent($eventId);
        }

        return view('public.standings.index', [
            'standings' => $standings,
            'topScorers' => $topScorers,
            'availableEvents' => $availableEvents,
            'selectedEvent' => $selectedEvent,
            'eventId' => $eventId,
        ]);
    }
}
