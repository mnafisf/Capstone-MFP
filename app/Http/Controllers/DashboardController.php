<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Lineup;
use App\Models\MatchModel;
use App\Models\Official;
use App\Models\Player;
use App\Models\Referee;
use App\Models\Role;
use App\Models\Stadium;
use App\Models\Transfer;
use App\Models\User;
use App\Services\CompetitionSettingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly CompetitionSettingService $settingService
    ) {
    }

    public function __invoke(): View
    {
        $role = optional(auth()->user()->role)->slug;

        return match ($role) {
            'club-admin' => $this->clubAdminDashboard(),
            'super-admin' => $this->superAdminDashboard(),
            'admin' => $this->adminDashboard(),
            default => view('dashboard', [
                'totalClubs' => Club::count(),
                'totalPlayers' => Player::count(),
                'totalMatches' => MatchModel::count(),
                'totalEvents' => Event::count(),
            ]),
        };
    }

    private function adminDashboard(): View
    {
        return view('dashboard.admin', $this->panitiaDashboardData());
    }

    private function superAdminDashboard(): View
    {
        $adminRoleId = Role::query()->where('slug', 'admin')->value('id');
        $clubAdminRoleId = Role::query()->where('slug', 'club-admin')->value('id');

        return view('dashboard.super-admin', array_merge($this->panitiaDashboardData(), [
            'systemStats' => [
                'admins' => $adminRoleId ? User::query()->where('role_id', $adminRoleId)->count() : 0,
                'club_admins' => $clubAdminRoleId ? User::query()->where('role_id', $clubAdminRoleId)->count() : 0,
                'stadiums' => Stadium::query()->count(),
                'referees' => Referee::query()->count(),
                'transfer_pending' => Transfer::query()->where('verification_status', 'pending')->count(),
                'clubs_active' => Club::query()->where('is_active', true)->count(),
            ],
        ]));
    }

    private function panitiaDashboardData(): array
    {
        $pendingPlayers = Player::query()
            ->with('club')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $pendingOfficials = Official::query()
            ->with('club')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $upcomingMatches = MatchModel::query()
            ->with(['homeClub', 'awayClub', 'event', 'stadium'])
            ->where('match_date', '>=', now())
            ->orderBy('match_date')
            ->limit(5)
            ->get();

        $nextLineupDeadline = MatchModel::query()
            ->whereNotNull('lineup_deadline')
            ->where('lineup_deadline', '>=', now())
            ->orderBy('lineup_deadline')
            ->value('lineup_deadline');

        // Satu query agregat untuk seluruh hitungan pemain.
        $playerCounts = Player::query()
            ->selectRaw('COUNT(*) as total, SUM(status = ?) as approved, SUM(status = ?) as pending', ['approved', 'pending'])
            ->first();

        // Satu query agregat untuk seluruh hitungan official.
        $officialCounts = Official::query()
            ->selectRaw('COUNT(*) as total, SUM(status = ?) as pending, SUM(status = ?) as approved', ['pending', 'approved'])
            ->first();

        // Satu query agregat untuk statistik pertandingan & event.
        $eventCounts = Event::query()->count();
        $matchCounts = MatchModel::query()
            ->selectRaw('COUNT(*) as total, SUM(match_date >= ?) as upcoming', [now()])
            ->first();

        $playersTotal = (int) ($playerCounts->total ?? 0);
        $playersApproved = (int) ($playerCounts->approved ?? 0);

        return [
            'stats' => [
                'clubs_total' => Club::query()->count(),
                'players_total' => $playersTotal,
                'players_pending' => (int) ($playerCounts->pending ?? 0),
                'players_approved' => $playersApproved,
                'officials_pending' => (int) ($officialCounts->pending ?? 0),
                'officials_approved' => (int) ($officialCounts->approved ?? 0),
                'events_total' => $eventCounts,
                'matches_total' => (int) ($matchCounts->total ?? 0),
                'matches_upcoming' => (int) ($matchCounts->upcoming ?? 0),
                'registrations' => EventRegistration::query()->count(),
            ],
            'playerApprovalPct' => $playersTotal > 0 ? (int) round(($playersApproved / $playersTotal) * 100) : 0,
            'pendingPlayers' => $pendingPlayers,
            'pendingOfficials' => $pendingOfficials,
            'upcomingMatches' => $upcomingMatches,
            'lineupDeadline' => $nextLineupDeadline,
        ];
    }

    private function clubAdminDashboard(): View
    {
        $user = auth()->user();
        $club = Club::query()->findOrFail($user->club_id);
        $clubId = $club->id;

        $playersQuery = Player::query()->where('club_id', $clubId);
        $officialsQuery = Official::query()->where('club_id', $clubId);

        // Agregat pemain & official dalam satu query masing-masing.
        $playerCounts = (clone $playersQuery)
            ->selectRaw('COUNT(*) as total, SUM(status = ?) as approved, SUM(status = ?) as pending, SUM(status = ?) as rejected', ['approved', 'pending', 'rejected'])
            ->first();

        $officialCounts = (clone $officialsQuery)
            ->selectRaw('COUNT(*) as total, SUM(status = ?) as approved, SUM(status = ?) as pending', ['approved', 'pending'])
            ->first();

        $upcomingMatches = MatchModel::query()
            ->with(['homeClub', 'awayClub', 'event', 'stadium'])
            ->where(function ($q) use ($clubId) {
                $q->where('home_club_id', $clubId)->orWhere('away_club_id', $clubId);
            })
            ->where('match_date', '>=', now())
            ->orderBy('match_date')
            ->limit(5)
            ->get();

        $activeTransfers = Transfer::query()
            ->where(function ($q) use ($clubId) {
                $q->where('from_club_id', $clubId)->orWhere('to_club_id', $clubId);
            })
            ->whereNull('accepted_at')
            ->count();

        $lineupDeadlineQuery = MatchModel::query()
            ->where(function ($q) use ($clubId) {
                $q->where('home_club_id', $clubId)->orWhere('away_club_id', $clubId);
            })
            ->where('match_date', '>=', now());

        $lineupDeadline = (clone $lineupDeadlineQuery)
            ->whereNotNull('lineup_deadline')
            ->where('lineup_deadline', '>=', now())
            ->orderBy('lineup_deadline')
            ->value('lineup_deadline');

        return view('dashboard.club-admin', [
            'club' => $club,
            'stats' => [
                'players_total' => (int) ($playerCounts->total ?? 0),
                'players_approved' => (int) ($playerCounts->approved ?? 0),
                'players_pending' => (int) ($playerCounts->pending ?? 0),
                'players_rejected' => (int) ($playerCounts->rejected ?? 0),
                'officials_total' => (int) ($officialCounts->total ?? 0),
                'officials_approved' => (int) ($officialCounts->approved ?? 0),
                'officials_pending' => (int) ($officialCounts->pending ?? 0),
                'event_registrations' => EventRegistration::query()
                    ->whereHas('player', fn ($q) => $q->where('club_id', $clubId))
                    ->count(),
                'lineups' => Lineup::query()->where('club_id', $clubId)->count(),
                'active_transfers' => $activeTransfers,
                'active_events' => Event::query()->count(),
            ],
            'upcomingMatches' => $upcomingMatches,
            'canManageLineups' => $lineupDeadline !== null,
            'lineupDeadline' => $lineupDeadline,
        ]);
    }
}
