<?php

namespace App\Http\Controllers\ClubAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubAdmin\StoreEventRegistrationRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventRegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $clubId = $request->user()->club_id;

        return view('club-admin.events.index', [
            'events' => Event::query()->where('is_active', true)->orderBy('name')->get(),
            'players' => Player::where('club_id', $clubId)->where('status', 'approved')->orderBy('name')->get(),
            'registrations' => EventRegistration::query()
                ->with(['event', 'player'])
                ->whereHas('player', fn ($q) => $q->where('club_id', $clubId))
                ->latest()
                ->paginate(10),
        ]);
    }

    public function store(StoreEventRegistrationRequest $request): RedirectResponse
    {
        $event = Event::findOrFail($request->validated('event_id'));
        $playerIds = $request->validated('player_ids');

        $created = 0;
        $already = 0;

        foreach ($playerIds as $playerId) {
            $registration = EventRegistration::firstOrCreate([
                'event_id' => $event->id,
                'player_id' => $playerId,
            ]);

            if ($registration->wasRecentlyCreated) {
                $created++;
            } else {
                $already++;
            }
        }

        $message = match (true) {
            $created > 0 && $already > 0 => "{$created} pemain baru didaftarkan. {$already} pemain sudah terdaftar sebelumnya.",
            $created > 0 => "{$created} pemain berhasil didaftarkan ke event.",
            default => 'Semua pemain yang dipilih sudah terdaftar di event ini.',
        };

        return back()->with('success', $message);
    }
}
