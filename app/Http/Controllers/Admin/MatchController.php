<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMatchCardRequest;
use App\Http\Requests\Admin\StoreMatchGoalRequest;
use App\Http\Requests\Admin\StoreMatchRequest;
use App\Http\Requests\Admin\StoreMatchSubstitutionRequest;
use App\Models\Club;
use App\Models\Event;
use App\Models\EventBracketMatch;
use App\Models\MatchCard;
use App\Models\MatchGoal;
use App\Models\MatchModel;
use App\Models\MatchSubstitution;
use App\Models\Player;
use App\Models\Referee;
use App\Models\Stadium;
use App\Repositories\MatchRepository;
use App\Services\MatchService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function __construct(
        private readonly MatchRepository $matchRepository,
        private readonly MatchService $matchService
    ) {}

    public function index(Request $request): View
    {
        $matches = $this->matchRepository->paginate($request->search);

        return view('admin.matches.index', [
            'matches' => $matches,
            'events' => Event::query()->orderBy('name')->get(),
            'clubs' => Club::query()->where('is_active', true)->orderBy('name')->get(),
            'stadiums' => Stadium::query()->where('is_active', true)->orderBy('name')->get(),
            'referees' => Referee::query()->orderBy('name')->get(),
        ]);
    }

    public function show(MatchModel $match): View
    {
        $match->load([
            'event',
            'homeClub',
            'awayClub',
            'stadium',
            'referee',
            'referee2',
            'referee3',
            'result',
            'goals.player',
            'cards.player',
            'substitutions.playerOut',
            'substitutions.playerIn',
        ]);

        $homeLineup = $match->lineups()->with(['lineupPlayers.player'])->where('club_id', $match->home_club_id)->first();
        $awayLineup = $match->lineups()->with(['lineupPlayers.player'])->where('club_id', $match->away_club_id)->first();

        $homeLineupPlayers = $homeLineup?->lineupPlayers ?? collect();
        $awayLineupPlayers = $awayLineup?->lineupPlayers ?? collect();

        $homeStarters = $homeLineupPlayers->where('position_type', 'starter')->values();
        $homeSubstitutes = $homeLineupPlayers->where('position_type', 'substitute')->values();
        $awayStarters = $awayLineupPlayers->where('position_type', 'starter')->values();
        $awaySubstitutes = $awayLineupPlayers->where('position_type', 'substitute')->values();

        $homeSubstitutedOutIds = $match->substitutions->where('club_id', $match->home_club_id)->pluck('player_out_id')->map(fn ($id) => (int) $id)->all();
        $homeSubstitutedInIds = $match->substitutions->where('club_id', $match->home_club_id)->pluck('player_in_id')->map(fn ($id) => (int) $id)->all();
        $awaySubstitutedOutIds = $match->substitutions->where('club_id', $match->away_club_id)->pluck('player_out_id')->map(fn ($id) => (int) $id)->all();
        $awaySubstitutedInIds = $match->substitutions->where('club_id', $match->away_club_id)->pluck('player_in_id')->map(fn ($id) => (int) $id)->all();

        $homeActiveStarterIds = array_values(array_diff($homeStarters->pluck('player_id')->map(fn ($id) => (int) $id)->all(), $homeSubstitutedOutIds));
        $homeAvailableSubstitutes = $homeSubstitutes->reject(fn ($slot) => in_array((int) $slot->player_id, $homeSubstitutedInIds, true))->values();
        $homeActivePlayers = $homeLineupPlayers->filter(fn ($slot) => ($slot->position_type === 'starter' && in_array((int) $slot->player_id, $homeActiveStarterIds, true))
            || ($slot->position_type === 'substitute' && in_array((int) $slot->player_id, $homeSubstitutedInIds, true))
        )->values();
        $awayActiveStarterIds = array_values(array_diff($awayStarters->pluck('player_id')->map(fn ($id) => (int) $id)->all(), $awaySubstitutedOutIds));
        $awayAvailableSubstitutes = $awaySubstitutes->reject(fn ($slot) => in_array((int) $slot->player_id, $awaySubstitutedInIds, true))->values();
        $awayActivePlayers = $awayLineupPlayers->filter(fn ($slot) => ($slot->position_type === 'starter' && in_array((int) $slot->player_id, $awayActiveStarterIds, true))
            || ($slot->position_type === 'substitute' && in_array((int) $slot->player_id, $awaySubstitutedInIds, true))
        )->values();

        // Load all approved players from both clubs (auto-appear in dropdowns)
        $homeApprovedPlayers = Player::query()
            ->where('club_id', $match->home_club_id)
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();
        $awayApprovedPlayers = Player::query()
            ->where('club_id', $match->away_club_id)
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();

        // Build combined player lists for dropdowns: lineup players first (with position badge), then other approved players
        $homeLineupPlayerIds = $homeLineupPlayers->pluck('player_id')->map(fn ($id) => (int) $id)->all();
        $awayLineupPlayerIds = $awayLineupPlayers->pluck('player_id')->map(fn ($id) => (int) $id)->all();

        $homeSelectPlayers = $homeActivePlayers->map(function ($slot) {
            return [
                'id' => $slot->player_id,
                'name' => $slot->player?->name,
                'in_lineup' => true,
                'position_type' => $slot->position_type,
            ];
        })->values();

        $homeSelectPlayers = $homeSelectPlayers->concat(
            $homeApprovedPlayers
                ->whereNotIn('id', $homeLineupPlayerIds)
                ->map(function ($player) {
                    return [
                        'id' => $player->id,
                        'name' => $player->name,
                        'in_lineup' => false,
                        'position_type' => null,
                    ];
                })->values()
        );

        $awaySelectPlayers = $awayActivePlayers->map(function ($slot) {
            return [
                'id' => $slot->player_id,
                'name' => $slot->player?->name,
                'in_lineup' => true,
                'position_type' => $slot->position_type,
            ];
        })->values();

        $awaySelectPlayers = $awaySelectPlayers->concat(
            $awayApprovedPlayers
                ->whereNotIn('id', $awayLineupPlayerIds)
                ->map(function ($player) {
                    return [
                        'id' => $player->id,
                        'name' => $player->name,
                        'in_lineup' => false,
                        'position_type' => null,
                    ];
                })->values()
        );

        return view('admin.matches.show', [
            'match' => $match,
            'homeLineup' => $homeLineup,
            'awayLineup' => $awayLineup,
            'homeStarters' => $homeStarters,
            'homeSubstitutes' => $homeSubstitutes,
            'awayStarters' => $awayStarters,
            'awaySubstitutes' => $awaySubstitutes,
            'homeSubstitutedOutIds' => $homeSubstitutedOutIds,
            'homeSubstitutedInIds' => $homeSubstitutedInIds,
            'awaySubstitutedOutIds' => $awaySubstitutedOutIds,
            'awaySubstitutedInIds' => $awaySubstitutedInIds,
            'homeActiveStarterIds' => $homeActiveStarterIds,
            'homeAvailableSubstitutes' => $homeAvailableSubstitutes,
            'homeActivePlayers' => $homeActivePlayers,
            'awayActiveStarterIds' => $awayActiveStarterIds,
            'awayAvailableSubstitutes' => $awayAvailableSubstitutes,
            'awayActivePlayers' => $awayActivePlayers,
            'homeSelectPlayers' => $homeSelectPlayers,
            'awaySelectPlayers' => $awaySelectPlayers,
        ]);
    }

    public function store(StoreMatchRequest $request): RedirectResponse
    {
        $match = $this->matchService->create($request->validated());

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Pertandingan berhasil ditambahkan.');
    }

    public function update(StoreMatchRequest $request, MatchModel $match): RedirectResponse
    {
        $match->update($request->validated());

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Pertandingan berhasil diperbarui.');
    }

    public function destroy(MatchModel $match): RedirectResponse
    {
        $match->delete();

        return redirect()
            ->route('admin.matches.index')
            ->with('success', 'Pertandingan berhasil dihapus.');
    }

    public function createFromBracket(Event $event, EventBracketMatch $match): View
    {
        $match->load(['homeClub', 'awayClub']);

        return view('admin.matches.create_from_bracket', [
            'event' => $event,
            'bracketMatch' => $match,
            'stadiums' => Stadium::query()->where('is_active', true)->orderBy('name')->get(),
            'referees' => Referee::query()->orderBy('name')->get(),
        ]);
    }

    public function storeGoal(StoreMatchGoalRequest $request, MatchModel $match): RedirectResponse
    {
        $match->goals()->create([
            'player_id' => (int) $request->validated('player_id'),
            'club_id' => (int) $request->validated('club_id'),
            'minute_label' => $request->validated('minute_label'),
            'is_penalty' => $request->boolean('is_penalty'),
        ]);

        $this->matchService->syncResultFromGoals($match);

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Data gol berhasil ditambahkan.');
    }

    public function destroyGoal(MatchModel $match, MatchGoal $goal): RedirectResponse
    {
        if ($goal->match_id !== $match->id) {
            abort(404);
        }

        $goal->delete();

        $this->matchService->syncResultFromGoals($match);

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Data gol berhasil dihapus.');
    }

    public function storeCard(StoreMatchCardRequest $request, MatchModel $match): RedirectResponse
    {
        $match->cards()->create([
            'player_id' => (int) $request->validated('player_id'),
            'club_id' => (int) $request->validated('club_id'),
            'minute_label' => $request->validated('minute_label'),
            'card_type' => $request->validated('card_type'),
        ]);

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Kartu berhasil ditambahkan.');
    }

    public function destroyCard(MatchModel $match, MatchCard $card): RedirectResponse
    {
        if ($card->match_id !== $match->id) {
            abort(404);
        }

        $card->delete();

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Kartu berhasil dihapus.');
    }

    public function storeSubstitution(StoreMatchSubstitutionRequest $request, MatchModel $match): RedirectResponse
    {
        $match->substitutions()->create([
            'club_id' => (int) $request->validated('club_id'),
            'minute_label' => $request->validated('minute_label'),
            'player_out_id' => (int) $request->validated('player_out_id'),
            'player_in_id' => (int) $request->validated('player_in_id'),
        ]);

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Pergantian pemain berhasil ditambahkan.');
    }

    public function destroySubstitution(MatchModel $match, MatchSubstitution $substitution): RedirectResponse
    {
        if ($substitution->match_id !== $match->id) {
            abort(404);
        }

        $substitution->delete();

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Pergantian pemain berhasil dihapus.');
    }

    public function monitoring(Request $request): View
    {
        $events = Event::query()->orderBy('name')->get();
        $eventId = (int) $request->get('event_id');

        if (! $eventId) {
            return view('admin.monitoring.index', [
                'events' => $events,
                'event' => null,
                'clubs' => collect(),
                'standings' => collect(),
                'topScorers' => collect(),
                'historyMatches' => collect(),
            ]);
        }

        $event = Event::findOrFail($eventId);
        $standings = $this->matchService->standingsByEvent($event->id);
        $clubIds = $standings->pluck('club_id');

        return view('admin.monitoring.index', [
            'events' => $events,
            'event' => $event,
            'clubs' => Club::query()->whereIn('id', $clubIds)->pluck('name', 'id'),
            'standings' => $standings,
            'topScorers' => $this->matchService->topScorersByEvent($event->id),
            'historyMatches' => MatchModel::query()
                ->with(['homeClub', 'awayClub', 'result', 'goals.player'])
                ->where('event_id', $event->id)
                ->latest('match_date')
                ->get(),
        ]);
    }

    public function downloadDsp(MatchModel $match)
    {
        $match->loadMissing([
            'event',
            'homeClub',
            'awayClub',
            'stadium',
            'referee',
            'referee2',
            'referee3',
            'lineups.lineupPlayers.player',
            'homeClub.officials',
            'awayClub.officials',
        ]);

        $homeLineup = $match->lineups()->where('club_id', $match->home_club_id)->first();
        $awayLineup = $match->lineups()->where('club_id', $match->away_club_id)->first();

        $pdf = Pdf::loadView('admin.matches.dsp-pdf', [
            'match' => $match,
            'homeLineup' => $homeLineup,
            'awayLineup' => $awayLineup,
            'homeStarters' => $homeLineup ? $homeLineup->lineupPlayers->where('position_type', 'starter')->values() : collect(),
            'homeSubstitutes' => $homeLineup ? $homeLineup->lineupPlayers->where('position_type', 'substitute')->values() : collect(),
            'awayStarters' => $awayLineup ? $awayLineup->lineupPlayers->where('position_type', 'starter')->values() : collect(),
            'awaySubstitutes' => $awayLineup ? $awayLineup->lineupPlayers->where('position_type', 'substitute')->values() : collect(),
        ]);

        return $pdf->download('DSP-Match-'.$match->id.'.pdf');
    }

    public function updateSummary(Request $request, MatchModel $match): RedirectResponse
    {
        $match->update([
            'summary' => $request->input('summary'),
        ]);

        return redirect()
            ->route('admin.matches.show', $match)
            ->with('success', 'Ringkasan pertandingan berhasil disimpan.');
    }

    public function downloadSummary(MatchModel $match)
    {
        $match->loadMissing([
            'event',
            'homeClub',
            'homeClub.officials',
            'awayClub',
            'awayClub.officials',
            'stadium',
            'referee',
            'referee2',
            'referee3',
            'result',
            'goals.player',
            'cards.player',
            'substitutions.playerOut',
            'substitutions.playerIn',
            'lineups.lineupPlayers.player',
        ]);

        $homeLineup = $match->lineups()->where('club_id', $match->home_club_id)->first();
        $awayLineup = $match->lineups()->where('club_id', $match->away_club_id)->first();

        $homeStarters = $homeLineup ? $homeLineup->lineupPlayers->where('position_type', 'starter')->values() : collect();
        $homeSubstitutes = $homeLineup ? $homeLineup->lineupPlayers->where('position_type', 'substitute')->values() : collect();
        $awayStarters = $awayLineup ? $awayLineup->lineupPlayers->where('position_type', 'starter')->values() : collect();
        $awaySubstitutes = $awayLineup ? $awayLineup->lineupPlayers->where('position_type', 'substitute')->values() : collect();

        $homeSubstitutions = $match->substitutions->where('club_id', $match->home_club_id)->values();
        $awaySubstitutions = $match->substitutions->where('club_id', $match->away_club_id)->values();

        $pdf = Pdf::loadView('admin.matches.summary-pdf', [
            'match' => $match,
            'homeLineup' => $homeLineup,
            'awayLineup' => $awayLineup,
            'homeStarters' => $homeStarters,
            'homeSubstitutes' => $homeSubstitutes,
            'awayStarters' => $awayStarters,
            'awaySubstitutes' => $awaySubstitutes,
            'homeSubstitutions' => $homeSubstitutions,
            'awaySubstitutions' => $awaySubstitutions,
        ]);

        return $pdf->download('Match-Summary-'.$match->id.'.pdf');
    }
}
