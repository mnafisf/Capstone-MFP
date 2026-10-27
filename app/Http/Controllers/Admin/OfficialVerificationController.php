<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerifyOfficialRequest;
use App\Models\Club;
use App\Models\Official;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficialVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $clubId = $request->filled('club_id') ? $request->integer('club_id') : null;

        $clubs = Club::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $officials = Official::query()
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

        return view('admin.officials.verification.index', compact('officials', 'clubs', 'clubId'));
    }

    public function update(VerifyOfficialRequest $request, Official $official): RedirectResponse
    {
        $official->update($request->validated());

        return back()->with('success', 'Status verifikasi official berhasil diperbarui.');
    }
}
