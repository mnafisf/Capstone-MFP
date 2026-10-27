<?php

namespace App\Http\Controllers\ClubAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubAdmin\StoreLineupRequest;
use App\Models\Lineup;
use App\Models\LineupPlayer;
use App\Models\MatchModel;
use App\Models\Player;
use App\Services\PlayerDisciplineService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LineupController extends Controller
{
    public function __construct(private readonly PlayerDisciplineService $disciplineService) {}

    public function index(Request $request): View|RedirectResponse
    {
        $clubId = $request->user()->club_id;

        if ($request->filled('edit')) {
            $candidate = Lineup::query()
                ->with(['match.homeClub', 'match.awayClub', 'lineupPlayers'])
                ->where('club_id', $clubId)
                ->whereKey((int) $request->query('edit'))
                ->first();

            if ($candidate && ! $candidate->match->canManageLineups()) {
                return redirect()
                    ->route('club-admin.lineups.index')
                    ->withErrors(['deadline' => 'Batas waktu input susunan pemain untuk pertandingan ini sudah berakhir.']);
            }
        }

        $matchesForForm = MatchModel::query()
            ->with(['homeClub', 'awayClub'])
            ->where('match_date', '>', now())
            ->whereNotNull('lineup_deadline')
            ->where('lineup_deadline', '>=', now())
            ->where(function ($q) use ($clubId): void {
                $q->where('home_club_id', $clubId)->orWhere('away_club_id', $clubId);
            })
            ->orderBy('match_date')
            ->get();

        $editingLineup = null;
        if ($request->filled('edit')) {
            $candidate = Lineup::query()
                ->with(['match.homeClub', 'match.awayClub', 'lineupPlayers'])
                ->where('club_id', $clubId)
                ->whereKey((int) $request->query('edit'))
                ->first();

            if ($candidate && $candidate->match->canManageLineups()) {
                $editingLineup = $candidate;
            }
        }

        $players = Player::query()->where('club_id', $clubId)->where('status', 'approved')->orderBy('name')->get();
        $disciplineByMatch = $this->buildDisciplineByMatch($players, $matchesForForm, $clubId, $editingLineup);

        // Prepare default values for form
        $defaultPlayerStatuses = [];
        $defaultPlayerPositions = [];
        $defaultPlayerCaptains = [];

        if ($editingLineup) {
            foreach ($editingLineup->lineupPlayers as $lineupPlayer) {
                $playerId = $lineupPlayer->player_id;
                $defaultPlayerStatuses[$playerId] = $lineupPlayer->position_type;
                $defaultPlayerPositions[$playerId] = $lineupPlayer->field_position;
                $defaultPlayerCaptains[$playerId] = $lineupPlayer->is_captain;
            }
        }

        $selectedPlayerStatuses = (array) old('player_statuses', $defaultPlayerStatuses);
        $selectedPlayerPositions = (array) old('player_positions', $defaultPlayerPositions);
        $selectedPlayerCaptains = (array) old('player_captains', $defaultPlayerCaptains);

        return view('club-admin.lineups.index', [
            'matchesForForm' => $matchesForForm,
            'players' => $players,
            'lineups' => Lineup::query()
                ->with(['match.homeClub', 'match.awayClub', 'lineupPlayers.player'])
                ->where('club_id', $clubId)
                ->latest()
                ->paginate(10),
            'editingLineup' => $editingLineup,
            'positionOptions' => ['GK', 'CB', 'LB', 'RB', 'DMF', 'CMF', 'AMF', 'LWF', 'RWF', 'SS', 'CF'],
            'disciplineByMatch' => $disciplineByMatch,
            'selectedPlayerStatuses' => $selectedPlayerStatuses,
            'selectedPlayerPositions' => $selectedPlayerPositions,
            'selectedPlayerCaptains' => $selectedPlayerCaptains,
        ]);
    }

    public function store(StoreLineupRequest $request): RedirectResponse
    {
        $clubId = $request->user()->club_id;
        $match = MatchModel::query()->findOrFail($request->validated('match_id'));

        abort_unless(in_array($clubId, [$match->home_club_id, $match->away_club_id], true), 403);

        if (! $match->canManageLineups()) {
            throw ValidationException::withMessages(['deadline' => 'Batas waktu input lineup untuk pertandingan ini sudah berakhir atau belum diatur.']);
        }

        DB::transaction(function () use ($request, $clubId) {
            $starters = collect($request->validated('starters'))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $substitutes = collect($request->validated('substitutes', []))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->reject(fn ($id) => $starters->contains($id))
                ->values();

            $playerPositions = collect($request->input('player_positions', []))
                ->mapWithKeys(fn ($position, $playerId) => [(int) $playerId => $position ?: null]);

            $playerCaptains = collect($request->input('player_captains', []))
                ->mapWithKeys(fn ($isCaptain, $playerId) => [(int) $playerId => (bool) $isCaptain]);

            $lineup = Lineup::updateOrCreate(
                ['match_id' => $request->validated('match_id'), 'club_id' => $clubId],
                [
                    'shirt_color' => $request->validated('shirt_color'),
                    'pants_color' => $request->validated('pants_color'),
                    'socks_color' => $request->validated('socks_color'),
                ]
            );

            LineupPlayer::query()->where('lineup_id', $lineup->id)->delete();

            foreach ($starters as $playerId) {
                LineupPlayer::query()->create([
                    'lineup_id' => $lineup->id,
                    'player_id' => $playerId,
                    'position_type' => 'starter',
                    'field_position' => $playerPositions->get($playerId),
                    'is_captain' => $playerCaptains->get($playerId, false),
                ]);
            }

            foreach ($substitutes as $playerId) {
                LineupPlayer::query()->create([
                    'lineup_id' => $lineup->id,
                    'player_id' => $playerId,
                    'position_type' => 'substitute',
                    'field_position' => $playerPositions->get($playerId),
                    'is_captain' => $playerCaptains->get($playerId, false),
                ]);
            }
        });

        return redirect()
            ->route('club-admin.lineups.index')
            ->with('success', 'Susunan pemain berhasil disimpan.');
    }

    public function destroy(Request $request, Lineup $lineup): RedirectResponse
    {
        abort_unless((int) $lineup->club_id === (int) $request->user()->club_id, 403);

        $lineup->loadMissing('match');

        if (! $lineup->match->canManageLineups()) {
            return redirect()
                ->route('club-admin.lineups.index')
                ->withErrors(['lineup' => 'Susunan pemain tidak bisa dihapus karena deadline pertandingan ini sudah berakhir atau belum diatur.']);
        }

        $lineup->delete();

        return redirect()
            ->route('club-admin.lineups.index')
            ->with('success', 'Susunan pemain berhasil dihapus.');
    }

    public function downloadDsp(Request $request, Lineup $lineup)
    {
        abort_unless((int) $lineup->club_id === (int) $request->user()->club_id, 403);

        $lineup->loadMissing([
            'club',
            'match.event',
            'match.homeClub',
            'match.awayClub',
            'match.stadium',
            'match.referee',
            'lineupPlayers.player',
        ]);

        $pdf = Pdf::loadView('club-admin.lineups.dsp-pdf', [
            'lineup' => $lineup,
            'starters' => $lineup->lineupPlayers->where('position_type', 'starter')->values(),
            'substitutes' => $lineup->lineupPlayers->where('position_type', 'substitute')->values(),
        ]);

        return $pdf->download('DSP-Lineup-'.$lineup->id.'.pdf');
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Player>  $players
     * @param  \Illuminate\Support\Collection<int, MatchModel>  $matchesForForm
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function buildDisciplineByMatch($players, $matchesForForm, int $clubId, ?Lineup $editingLineup): array
    {
        $matches = $matchesForForm;

        if ($editingLineup) {
            $matches = $matchesForForm
                ->prepend($editingLineup->match)
                ->unique('id')
                ->values();
        }

        $disciplineByMatch = [];

        foreach ($matches as $match) {
            $match->loadMissing('event');

            foreach ($players as $player) {
                $disciplineByMatch[$match->id][$player->id] = $this->disciplineService->getStatusForMatch(
                    (int) $player->id,
                    $match,
                    $clubId
                );
            }
        }

        return $disciplineByMatch;
    }
}
