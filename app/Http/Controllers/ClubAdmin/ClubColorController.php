<?php

namespace App\Http\Controllers\ClubAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubAdmin\StoreClubColorsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubColorController extends Controller
{
    public function edit(Request $request): View
    {
        $club = $request->user()->club;

        return view('club-admin.club.colors', [
            'club' => $club,
        ]);
    }

    public function update(StoreClubColorsRequest $request): RedirectResponse
    {
        $club = $request->user()->club;
        if ($club) {
            $club->update(array_filter($request->only(['shirt_color', 'pants_color', 'socks_color'])));
        }

        return back()->with('success', 'Warna jersey berhasil diperbarui.');
    }
}
