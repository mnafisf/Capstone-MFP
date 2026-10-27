<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreStadiumRequest;
use App\Models\Stadium;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StadiumController extends Controller
{
    public function index(Request $request): View
    {
        $stadiums = Stadium::query()
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.stadiums.index', compact('stadiums'));
    }

    public function create(): View
    {
        return view('super-admin.stadiums.create');
    }

    public function store(StoreStadiumRequest $request): RedirectResponse
    {
        Stadium::create($request->validated() + ['is_active' => (bool) $request->boolean('is_active', true)]);

        return redirect()
            ->route('super-admin.stadiums.index')
            ->with('success', 'Stadion berhasil ditambahkan.');
    }

    public function edit(Stadium $stadium): View
    {
        return view('super-admin.stadiums.edit', compact('stadium'));
    }

    public function update(StoreStadiumRequest $request, Stadium $stadium): RedirectResponse
    {
        $stadium->update($request->validated() + ['is_active' => (bool) $request->boolean('is_active', true)]);

        return redirect()
            ->route('super-admin.stadiums.index')
            ->with('success', 'Stadion berhasil diperbarui.');
    }

    public function toggle(Stadium $stadium): RedirectResponse
    {
        $stadium->update(['is_active' => ! $stadium->is_active]);

        return redirect()
            ->route('super-admin.stadiums.index')
            ->with('success', 'Status stadion berhasil diubah.');
    }

    public function destroy(Stadium $stadium): RedirectResponse
    {
        $stadium->delete();

        return redirect()
            ->route('super-admin.stadiums.index')
            ->with('success', 'Stadion berhasil dihapus.');
    }
}
