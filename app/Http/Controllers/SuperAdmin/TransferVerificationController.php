<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\VerifyTransferRequest;
use App\Models\Transfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class TransferVerificationController extends Controller
{
    public function __construct(private readonly \App\Services\TransferService $transferService)
    {
    }

    public function index(Request $request): View
    {
        $transfers = Transfer::query()
            ->with(['player.club', 'fromClub', 'toClub'])
            ->where('verification_status', 'pending')
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->whereHas('player', fn ($playerQuery) => $playerQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('fromClub', fn ($clubQuery) => $clubQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('toClub', fn ($clubQuery) => $clubQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.transfers.verification.index', compact('transfers'));
    }

    public function update(VerifyTransferRequest $request, Transfer $transfer): RedirectResponse
    {
        if ($request->status === 'approved') {
            $this->transferService->approveBySuperAdmin($transfer, $request->verification_note);
        } else {
            $this->transferService->rejectBySuperAdmin($transfer, $request->verification_note);
        }

        return back()->with('success', 'Verifikasi mutasi berhasil diperbarui.');
    }

    public function preview(Transfer $transfer): Response
    {
        abort_unless($transfer->document_path, 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($transfer->document_path), 404);

        $fullPath = $disk->path($transfer->document_path);
        $mimeType = $disk->mimeType($transfer->document_path) ?: 'application/pdf';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.basename($transfer->document_path).'"',
        ]);
    }
}
