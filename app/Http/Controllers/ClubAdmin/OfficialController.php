<?php

namespace App\Http\Controllers\ClubAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubAdmin\StoreOfficialRequest;
use App\Models\Official;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficialController extends Controller
{
    public function index(Request $request): View
    {
        $clubId = $request->user()->club_id;

        $officials = Official::query()
            ->where('club_id', $clubId)
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $editingOfficial = null;
        if ($request->filled('edit_official_id')) {
            $editingOfficial = Official::query()
                ->where('club_id', $clubId)
                ->find($request->integer('edit_official_id'));
        }

        return view('club-admin.officials.index', [
            'officials' => $officials,
            'editingOfficial' => $editingOfficial,
            'positions' => Official::POSITIONS,
        ]);
    }

    public function store(StoreOfficialRequest $request): RedirectResponse
    {
        Official::create([
            ...$request->validated(),
            'club_id' => $request->user()->club_id,
            'status' => 'pending',
        ]);
        
        return back()->with('success', 'Official berhasil diajukan dan menunggu verifikasi admin.');
    }

    public function update(StoreOfficialRequest $request, Official $official): RedirectResponse
    {
        $this->authorizeClubOwnership($request, $official);
        abort_unless($official->isPending(), 403, 'Official yang sudah diverifikasi tidak dapat diubah.');

        $official->update([
            ...$request->validated(),
            'status' => 'pending',
            'verification_note' => null,
        ]);
        
        return back()->with('success', 'Data official berhasil diperbarui.');
    }

    public function destroy(Request $request, Official $official): RedirectResponse
    {
        $this->authorizeClubOwnership($request, $official);
        abort_unless($official->isPending(), 403, 'Official yang sudah diverifikasi tidak dapat dihapus.');

        $official->delete();

        return back()->with('success', 'Official berhasil dihapus.');
    }

    private function authorizeClubOwnership(Request $request, Official $official): void
    {
        abort_if($request->user()->club_id !== $official->club_id, 403);
    }
}
