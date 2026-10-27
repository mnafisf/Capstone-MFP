<?php

namespace App\Http\Controllers\ClubAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubAdmin\StorePlayerRequest;
use App\Models\Player;
use App\Repositories\PlayerRepository;
use App\Services\PlayerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly PlayerService $playerService
    ) {
    }

    public function index(Request $request): View
    {
        $players = $this->playerRepository->paginateForClub($request->user()->club_id, $request->search);
        $editingPlayer = null;
        if ($request->filled('edit_player_id')) {
            $editingPlayer = Player::query()
                ->where('club_id', $request->user()->club_id)
                ->find($request->integer('edit_player_id'));
        }
        $indonesianProvinces = [
            'Aceh',
            'Sumatera Utara',
            'Sumatera Barat',
            'Riau',
            'Kepulauan Riau',
            'Jambi',
            'Sumatera Selatan',
            'Kepulauan Bangka Belitung',
            'Bengkulu',
            'Lampung',
            'DKI Jakarta',
            'Jawa Barat',
            'Jawa Tengah',
            'DI Yogyakarta',
            'Jawa Timur',
            'Banten',
            'Bali',
            'Nusa Tenggara Barat',
            'Nusa Tenggara Timur',
            'Kalimantan Barat',
            'Kalimantan Tengah',
            'Kalimantan Selatan',
            'Kalimantan Timur',
            'Kalimantan Utara',
            'Sulawesi Utara',
            'Gorontalo',
            'Sulawesi Tengah',
            'Sulawesi Barat',
            'Sulawesi Selatan',
            'Sulawesi Tenggara',
            'Maluku',
            'Maluku Utara',
            'Papua',
            'Papua Barat',
            'Papua Barat Daya',
            'Papua Selatan',
            'Papua Tengah',
            'Papua Pegunungan',
        ];

        return view('club-admin.players.index', compact('players', 'editingPlayer', 'indonesianProvinces'));
    }

    public function store(StorePlayerRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $isDraft = ($payload['submit_action'] ?? 'final') === 'draft';
        unset($payload['submit_action']);

        if ($isDraft) {
            $this->applyDraftDefaults($payload);
        }

        $this->attachUploadedDocuments($request, $payload);
        $this->playerService->createForClub($request->user()->club_id, $payload);

        $message = $isDraft
            ? 'Draft pemain berhasil disimpan.'
            : 'Pemain berhasil ditambahkan.';

        return back()->with('success', $message);
    }

    public function update(StorePlayerRequest $request, Player $player): RedirectResponse
    {
        $this->authorizeClubOwnership($request, $player);

        $payload = $request->validated();
        unset($payload['submit_action']);
        $this->attachUploadedDocuments($request, $payload);

        $this->playerService->update($player, $payload);

        return back()->with('success', 'Pemain berhasil diperbarui.');
    }

    public function destroy(Request $request, Player $player): RedirectResponse
    {
        $this->authorizeClubOwnership($request, $player);
        $player->delete();

        return back()->with('success', 'Pemain berhasil dihapus.');
    }

    private function authorizeClubOwnership(Request $request, Player $player): void
    {
        abort_if($request->user()->club_id !== $player->club_id, 403);
    }

    private function attachUploadedDocuments(StorePlayerRequest $request, array &$payload): void
    {
        foreach ([
            'photo',
            'family_card',
            'club_letter',
            'birth_certificate',
            'report_card',
            'privacy_consent_letter',
        ] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $payload[$fileKey.'_path'] = $request->file($fileKey)->store('players', 'public');
            }
        }
    }

    private function applyDraftDefaults(array &$payload): void
    {
        $payload['name'] = $payload['name'] ?: 'Draft Pemain '.now()->format('YmdHis');
        $payload['nik'] = $payload['nik'] ?: 'DRAFT-'.Str::upper(Str::random(12));
        $payload['birth_place_date'] = $payload['birth_place_date']
            ?: 'Draft '.now()->locale('id')->translatedFormat('d F Y');
    }
}
