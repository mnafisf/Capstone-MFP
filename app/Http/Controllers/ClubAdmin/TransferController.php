<?php

namespace App\Http\Controllers\ClubAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubAdmin\StoreTransferRequest;
use App\Models\Club;
use App\Models\Player;
use App\Repositories\TransferRepository;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransferRepository $transferRepository,
        private readonly TransferService $transferService
    ) {
    }

    public function index(Request $request): View
    {
        return view('club-admin.transfers.index', [
            'transfers' => $this->transferRepository->paginateForClub($request->user()->club_id),
            'clubs' => Club::query()->where('is_active', true)->orderBy('name')->get(),
            'players' => Player::query()
                ->where('club_id', $request->user()->club_id)
                ->orderBy('name')
                ->get(['id', 'name', 'nik']),
        ]);
    }

    public function store(StoreTransferRequest $request): RedirectResponse
    {
        $player = Player::query()
            ->where('id', $request->integer('player_id'))
            ->where('club_id', $request->user()->club_id)
            ->firstOrFail();
        $path = $request->file('document')?->store('transfers', 'public');

        $this->transferService->request($player, (int) $request->to_club_id, $path);

        return back()->with('success', 'Pengajuan mutasi pemain berhasil dibuat.');
    }
}
