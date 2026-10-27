<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreClubRequest;
use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class ClubManagementController extends Controller
{
    public function index(Request $request): View
    {
        $clubs = Club::query()
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.clubs.index', compact('clubs'));
    }

    public function store(StoreClubRequest $request): RedirectResponse
    {
        Club::create([
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => (bool) $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Klub berhasil ditambahkan.');
    }

    public function update(StoreClubRequest $request, Club $club): RedirectResponse
    {
        $club->update([
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => (bool) $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Klub berhasil diperbarui.');
    }

    public function toggle(Club $club): RedirectResponse
    {
        $club->update(['is_active' => ! $club->is_active]);

        return back()->with('success', 'Status klub berhasil diubah.');
    }

    public function destroy(Club $club): RedirectResponse
    {
        try {
            $club->delete();
        } catch (QueryException) {
            return back()->withErrors([
                'club_delete' => 'Klub tidak bisa dihapus karena masih dipakai oleh data pertandingan atau pemain.',
            ]);
        }

        return back()->with('success', 'Klub berhasil dihapus.');
    }

    public function logo(Club $club): Response
    {
        abort_unless(filled($club->logo_path), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($club->logo_path), 404);

        $fullPath = $disk->path($club->logo_path);
        $mimeType = $disk->mimeType($club->logo_path) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.basename($club->logo_path).'"',
        ]);
    }
}
