<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateCompetitionSettingRequest;
use App\Models\Event;
use App\Models\MatchModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CompetitionSettingController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::query()->orderBy('name')->get();
        $eventId = (int) $request->query('event_id');

        $matches = MatchModel::query()
            ->with(['event', 'homeClub', 'awayClub'])
            ->orderBy('match_date')
            ->when($eventId, fn ($query) => $query->where('event_id', $eventId))
            ->get();

        return view('super-admin.settings.index', [
            'matches' => $matches,
            'events' => $events,
            'selectedEventId' => $eventId,
        ]);
    }

    public function edit(MatchModel $match): View
    {
        $match->loadMissing(['event', 'homeClub', 'awayClub']);

        return view('super-admin.settings.edit', [
            'match' => $match,
        ]);
    }

    public function update(UpdateCompetitionSettingRequest $request, MatchModel $match): RedirectResponse
    {
        $deadline = Carbon::parse($request->validated('lineup_deadline'));

        if ($deadline->greaterThanOrEqualTo($match->match_date)) {
            throw ValidationException::withMessages([
                'lineup_deadline' => 'Deadline lineup harus lebih awal dari waktu pertandingan.',
            ]);
        }

        $match->update([
            'lineup_deadline' => $deadline,
        ]);

        return redirect()
            ->route('super-admin.settings.edit', $match)
            ->with('success', 'Deadline lineup pertandingan berhasil diperbarui.');
    }
}
