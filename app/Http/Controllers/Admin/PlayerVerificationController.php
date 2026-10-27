<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerifyPlayerRequest;
use App\Models\Club;
use App\Models\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class PlayerVerificationController extends Controller
{
    private const DOCUMENT_FIELDS = [
        'photo_path',
        'family_card_path',
        'club_letter_path',
        'birth_certificate_path',
        'report_card_path',
        'privacy_consent_letter_path',
    ];

    public function index(Request $request): View
    {
        $clubId = $request->filled('club_id') ? $request->integer('club_id') : null;

        $clubs = Club::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $players = Player::query()
            ->with('club')
            ->when(
                $clubId,
                fn ($q) => $q->where('club_id', $clubId)->where('status', 'pending'),
                fn ($q) => $q->whereRaw('0 = 1'),
            )
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.verification.index', compact('players', 'clubs', 'clubId'));
    }

    public function update(VerifyPlayerRequest $request, Player $player): RedirectResponse
    {
        $player->update($request->validated());

        return back()->with('success', 'Status verifikasi pemain berhasil diperbarui.');
    }

    public function previewDocument(Player $player, string $field): Response
    {
        abort_unless(in_array($field, self::DOCUMENT_FIELDS, true), 404);

        $path = $player->{$field};
        abort_unless(filled($path), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        $fullPath = $disk->path($path);
        $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
