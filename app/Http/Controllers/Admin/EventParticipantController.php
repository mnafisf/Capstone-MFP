<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenerateEventBracketRequest;
use App\Http\Requests\Admin\RecordBracketWinnerRequest;
use App\Http\Requests\Admin\StoreEventParticipantsRequest;
use App\Http\Requests\Admin\UpdateBracketSlotsRequest;
use App\Models\Club;
use App\Models\Event;
use App\Models\EventBracketMatch;
use App\Services\BracketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventParticipantController extends Controller
{
    public function __construct(private readonly BracketService $bracketService) {}

    public function index(Event $event): View
    {
        $event->load([
            'participants' => fn ($query) => $query->with('club')->orderBy('seed')->orderBy('id'),
            'bracketRounds' => [
                'matches' => fn ($query) => $query->with(['homeClub', 'awayClub', 'winnerClub']),
            ],
        ]);

        return view('admin.events.teams', [
            'event' => $event,
            'availableClubs' => $this->bracketService->availableClubs($event),
            'bracketSizeOptions' => $this->bracketSizeOptions(),
        ]);
    }

    public function store(StoreEventParticipantsRequest $request, Event $event): RedirectResponse
    {
        $clubIds = array_map('intval', $request->validated()['club_ids'] ?? []);

        DB::transaction(function () use ($event, $clubIds): void {
            $event->participants()->delete();
            $event->participants()->createMany(
                array_map(fn (int $clubId): array => ['club_id' => $clubId], $clubIds),
            );

            if ($event->isKnockout() && $event->hasBracket()) {
                $event->participants()->orderBy('id')->get()
                    ->each(fn ($participant, int $index) => $participant->update(['seed' => $index + 1]));
            }
        });

        return back()->with('success', count($clubIds) > 0
            ? count($clubIds).' klub disimpan sebagai peserta event.'
            : 'Daftar peserta event dikosongkan.');
    }

    public function generateBracket(GenerateEventBracketRequest $request, Event $event): RedirectResponse
    {
        $teamCount = (int) $request->validated()['team_count'];

        $this->bracketService->generate($event, $teamCount);

        return back()->with('success', 'Bagan knockout dengan '.$teamCount.' tim berhasil dibuat. Silakan tentukan tim pada setiap slot.');
    }

    public function updateSlots(UpdateBracketSlotsRequest $request, Event $event): RedirectResponse
    {
        $this->bracketService->assignFirstRoundSlots($event->load('bracketRounds.matches'), $request->slotPayload());

        return back()->with('success', 'Susunan tim babak pertama tersimpan.');
    }

    public function recordWinner(RecordBracketWinnerRequest $request, Event $event, EventBracketMatch $match): RedirectResponse
    {
        $this->bracketService->recordWinner($match, Club::findOrFail((int) $request->validated()['winner_club_id']));

        return back()->with('success', 'Pemenang '.$match->round->name.' berhasil dicatat.');
    }

    public function destroyBracket(Event $event): RedirectResponse
    {
        $this->bracketService->reset($event);

        return back()->with('success', 'Bagan knockout dihapus. Tim peserta tetap tersimpan.');
    }

    /**
     * @return list<int>
     */
    private function bracketSizeOptions(): array
    {
        $options = [];

        for ($size = 4; $size <= 64; $size *= 2) {
            $options[] = $size;
        }

        return $options;
    }
}
