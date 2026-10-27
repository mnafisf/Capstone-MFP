<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreRefereeRequest;
use App\Models\Referee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefereeController extends Controller
{
    public function index(Request $request): View
    {
        $referees = Referee::query()
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.referees.index', compact('referees'));
    }

    public function create(): View
    {
        return view('super-admin.referees.create');
    }

    public function store(StoreRefereeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('ktp')) {
            $data['ktp_path'] = $request->file('ktp')->store('referees/ktp', 'public');
        }
        if ($request->hasFile('license_certificate')) {
            $data['license_certificate_path'] = $request->file('license_certificate')->store('referees/licenses', 'public');
        }

        Referee::create($data);

        return back()->with('success', 'Wasit berhasil ditambahkan.');
    }

    public function update(StoreRefereeRequest $request, Referee $referee): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('ktp')) {
            $data['ktp_path'] = $request->file('ktp')->store('referees/ktp', 'public');
        }
        if ($request->hasFile('license_certificate')) {
            $data['license_certificate_path'] = $request->file('license_certificate')->store('referees/licenses', 'public');
        }

        $referee->update($data);

        return back()->with('success', 'Wasit berhasil diperbarui.');
    }

    public function destroy(Referee $referee): RedirectResponse
    {
        $referee->delete();

        return back()->with('success', 'Wasit berhasil dihapus.');
    }

    public function show(Referee $referee): View
    {
        return view('super-admin.referees.show', compact('referee'));
    }
}
