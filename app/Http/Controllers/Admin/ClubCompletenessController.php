<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubCompletenessController extends Controller
{
    public function index(Request $request): View
    {
        $clubs = Club::query()
            ->with(['players', 'officials'])
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $completenessData = $clubs->getCollection()->map(function ($club) {
            $totalPlayers = $club->players->count();
            $approvedPlayers = $club->players->where('status', 'approved')->count();
            $pendingPlayers = $club->players->where('status', 'pending')->count();

            $totalOfficials = $club->officials->count();
            $approvedOfficials = $club->officials->where('status', 'approved')->count();
            $pendingOfficials = $club->officials->where('status', 'pending')->count();

            $combinedTotal = $totalPlayers + $totalOfficials;
            $combinedApproved = $approvedPlayers + $approvedOfficials;

            return [
                'club' => $club,
                'total_players' => $totalPlayers,
                'approved_players' => $approvedPlayers,
                'rejected_players' => $club->players->where('status', 'rejected')->count(),
                'pending_players' => $pendingPlayers,
                'approved_ratio' => $totalPlayers > 0
                    ? round(($approvedPlayers / $totalPlayers) * 100, 1)
                    : 0,
                'total_officials' => $totalOfficials,
                'approved_officials' => $approvedOfficials,
                'pending_officials' => $pendingOfficials,
                'combined_ratio' => $combinedTotal > 0 ? round(($combinedApproved / $combinedTotal) * 100, 1) : 0,
            ];
        });

        return view('admin.club-completeness.index', [
            'clubs' => $clubs,
            'completenessData' => $completenessData,
        ]);
    }

    public function show(Club $club): View
    {
        $club->loadMissing(['players', 'officials']);

        $playerStats = [
            'approved' => $club->players->where('status', 'approved')->count(),
            'rejected' => $club->players->where('status', 'rejected')->count(),
            'pending' => $club->players->where('status', 'pending')->count(),
            'total' => $club->players->count(),
        ];

        $playerStats['approved_ratio'] = $playerStats['total'] > 0
            ? round(($playerStats['approved'] / $playerStats['total']) * 100, 1)
            : 0;

        $officialStats = [
            'approved' => $club->officials->where('status', 'approved')->count(),
            'pending' => $club->officials->where('status', 'pending')->count(),
            'total' => $club->officials->count(),
        ];

        $officialStats['approved_ratio'] = $officialStats['total'] > 0
            ? round(($officialStats['approved'] / $officialStats['total']) * 100, 1)
            : 0;

        $players = $club->players()
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'rejected' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END")
            ->latest('updated_at')
            ->paginate(5, ['*'], 'players_page');

        $combinedTotal = $playerStats['total'] + $officialStats['total'];
        $combinedApproved = $playerStats['approved'] + $officialStats['approved'];
        $combinedRatio = $combinedTotal > 0 ? round(($combinedApproved / $combinedTotal) * 100, 1) : 0;

        return view('admin.club-completeness.show', [
            'club' => $club,
            'officials' => $club->officials()
                ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'rejected' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END")
                ->latest('updated_at')
                ->paginate(5, ['*'], 'officials_page'),
            'players' => $players,
            'playerStats' => $playerStats,
            'officialStats' => $officialStats,
            'combinedRatio' => $combinedRatio,
        ]);
    }

    public function playerShow(Club $club, \App\Models\Player $player): \Illuminate\View\View
    {
        abort_unless($player->club_id === $club->id, 404);

        return view('admin.club-completeness.player-show', [
            'club' => $club,
            'player' => $player,
        ]);
    }

    public function playerDestroy(Club $club, \App\Models\Player $player)
    {
        abort_unless($player->club_id === $club->id, 404);
        $player->delete();

        return redirect()->route('admin.club-completeness.show', $club)->with('success', 'Pemain berhasil dihapus.');
    }

    public function officialShow(Club $club, \App\Models\Official $official): \Illuminate\View\View
    {
        abort_unless($official->club_id === $club->id, 404);

        return view('admin.club-completeness.official-show', [
            'club' => $club,
            'official' => $official,
        ]);
    }

    public function officialDestroy(Club $club, \App\Models\Official $official)
    {
        abort_unless($official->club_id === $club->id, 404);
        $official->delete();

        return redirect()->route('admin.club-completeness.show', $club)->with('success', 'Official berhasil dihapus.');
    }
}
