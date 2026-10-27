<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use App\Services\BracketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventParticipantBracketTest extends TestCase
{
    use RefreshDatabase;

    private function actAsAdmin(): void
    {
        $role = Role::create(['name' => 'Admin Panitia', 'slug' => 'admin']);

        $this->actingAs(User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role_id' => $role->id,
            'password' => 'password',
        ]));
    }

    private function clubs(int $count = 8, string $prefix = 'K'): array
    {
        $clubs = [];

        for ($i = 1; $i <= $count; $i++) {
            $clubs[] = Club::create([
                'name' => $prefix.'lub '.$i,
                'code' => $prefix.$i,
                'is_active' => true,
            ]);
        }

        return $clubs;
    }

    private function event(string $type = 'league'): Event
    {
        return Event::create([
            'name' => 'Liga U-15 '.$type,
            'competition_type' => $type,
            'min_age' => 12,
            'max_age' => 15,
            'is_active' => true,
            'yellow_card_threshold' => 2,
            'yellow_card_ban_matches' => 1,
            'red_card_ban_matches' => 2,
        ]);
    }

    /**
     * @return list<int>
     */
    private function clubIds(array $clubs): array
    {
        return array_map(fn (Club $club): int => $club->id, $clubs);
    }

    private function firstRound(Event $event)
    {
        return $event->bracketRounds()->where('round_number', 1)->firstOrFail()->matches;
    }

    public function test_admin_can_open_the_team_selection_page(): void
    {
        $event = $this->event();
        $this->actAsAdmin();

        $this->get(route('admin.events.teams.index', $event))
            ->assertOk()
            ->assertSee('Pilih Tim')
            ->assertSee($event->name);
    }

    public function test_admin_can_select_participating_clubs_for_a_league_event(): void
    {
        $event = $this->event();
        [$a, $b] = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.participants.store', $event), [
            'club_ids' => [$a->id, $b->id],
        ])->assertRedirect();

        $this->assertDatabaseCount('event_participants', 2);
        $this->assertDatabaseHas('event_participants', ['event_id' => $event->id, 'club_id' => $a->id]);
    }

    public function test_participant_selection_replaces_the_previous_selection(): void
    {
        $event = $this->event();
        $clubs = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.participants.store', $event), ['club_ids' => [$clubs[0]->id, $clubs[1]->id]]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => [$clubs[2]->id]]);

        $this->assertDatabaseCount('event_participants', 1);
        $this->assertDatabaseHas('event_participants', ['club_id' => $clubs[2]->id]);
    }

    public function test_bracket_is_generated_automatically_for_knockout(): void
    {
        $event = $this->event('knockout');
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 8])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $rounds = $event->bracketRounds()->orderBy('round_number')->get();

        $this->assertCount(3, $rounds);
        $this->assertSame(['Perempat Final', 'Semifinal', 'Final'], $rounds->pluck('name')->all());
        $this->assertSame([4, 2, 1], $rounds->pluck('match_count')->all());
        $this->assertDatabaseCount('event_bracket_matches', 7);
    }

    public function test_bracket_rounds_up_to_the_next_power_of_two(): void
    {
        $event = $this->event('knockout');
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 6]);

        $firstRound = $event->bracketRounds()->where('round_number', 1)->firstOrFail();

        $this->assertSame(4, $firstRound->match_count);
        $this->assertSame(3, $event->bracketRounds()->count());
    }

    public function test_bracket_cannot_be_generated_twice_or_for_a_league_event(): void
    {
        $knockout = $this->event('knockout');
        $league = $this->event('league');
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $knockout), ['team_count' => 4])
            ->assertSessionHasNoErrors();

        $this->post(route('admin.events.bracket.generate', $knockout), ['team_count' => 4])
            ->assertSessionHasErrors('team_count');

        $this->post(route('admin.events.bracket.generate', $league), ['team_count' => 4])
            ->assertSessionHasErrors('team_count');
    }

    public function test_slots_are_not_filled_automatically_when_participants_are_selected(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $placed = $this->firstRound($event)
            ->flatMap(fn ($match): array => [$match->home_club_id, $match->away_club_id])
            ->filter();

        $this->assertCount(0, $placed, 'Slot babak pertama harus tetap kosong sampai panitia menentukan sendiri timnya.');
    }

    public function test_admin_places_teams_into_first_round_slots_manually(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $matches = $this->firstRound($event)->values();
        $m1 = $matches[0];
        $m2 = $matches[1];

        // Admin menentukan sendiri pasangan timnya
        $this->patch(route('admin.events.bracket.slots', $event), [
            'slots' => [
                $m1->id => ['home' => $clubs[3]->id, 'away' => $clubs[0]->id],
                $m2->id => ['home' => $clubs[1]->id, 'away' => $clubs[2]->id],
            ],
        ])->assertRedirect();

        $m1->refresh();
        $m2->refresh();

        $this->assertSame($clubs[3]->id, $m1->home_club_id);
        $this->assertSame($clubs[0]->id, $m1->away_club_id);
        $this->assertSame($clubs[1]->id, $m2->home_club_id);
        $this->assertSame($clubs[2]->id, $m2->away_club_id);
    }

    public function test_a_club_cannot_be_placed_in_two_slots(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $matches = $this->firstRound($event)->values();

        $this->patch(route('admin.events.bracket.slots', $event), [
            'slots' => [
                $matches[0]->id => ['home' => $clubs[0]->id, 'away' => $clubs[1]->id],
                $matches[1]->id => ['home' => $clubs[2]->id, 'away' => $clubs[0]->id],
            ],
        ])->assertSessionHasErrors('slots');

        $this->assertNull($this->firstRound($event)->first()->home_club_id);
    }

    public function test_slots_only_accept_participating_clubs(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $outsider = $this->clubs(1, 'X')[0];
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $match = $this->firstRound($event)->first();

        $this->patch(route('admin.events.bracket.slots', $event), [
            'slots' => [$match->id => ['home' => $outsider->id, 'away' => null]],
        ])->assertSessionHasErrors('slots');

        $this->assertNull($match->fresh()->home_club_id);
    }

    public function test_changing_slots_clears_later_round_results(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $matches = $this->firstRound($event)->values();
        $m1 = $matches[0];
        $m2 = $matches[1];

        $this->patch(route('admin.events.bracket.slots', $event), [
            'slots' => [
                $m1->id => ['home' => $clubs[0]->id, 'away' => $clubs[1]->id],
                $m2->id => ['home' => $clubs[2]->id, 'away' => $clubs[3]->id],
            ],
        ]);

        $this->patch(route('admin.events.bracket.winner', [$event, $m1->fresh()]), ['winner_club_id' => $clubs[0]->id]);

        $final = $event->bracketRounds()->where('round_number', 2)->firstOrFail()->matches->first();
        $this->assertSame($clubs[0]->id, $final->home_club_id);

        // Ganti susunan slot -> hasil babak berikutnya harus dibersihkan
        $this->patch(route('admin.events.bracket.slots', $event), [
            'slots' => [
                $m1->id => ['home' => $clubs[1]->id, 'away' => $clubs[0]->id],
                $m2->id => ['home' => $clubs[2]->id, 'away' => $clubs[3]->id],
            ],
        ])->assertRedirect();

        $final->refresh();
        $this->assertNull($final->home_club_id);
        $this->assertNull($final->winner_club_id);
    }

    public function test_recording_a_winner_advances_the_club_to_the_next_round(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $matches = $this->firstRound($event)->values();
        $semifinal = $matches[0];

        $this->patch(route('admin.events.bracket.slots', $event), [
            'slots' => [
                $matches[0]->id => ['home' => $clubs[0]->id, 'away' => $clubs[1]->id],
                $matches[1]->id => ['home' => $clubs[2]->id, 'away' => $clubs[3]->id],
            ],
        ]);

        $winner = $clubs[0];

        $this->patch(route('admin.events.bracket.winner', [$event, $semifinal->fresh()]), ['winner_club_id' => $winner->id])
            ->assertRedirect();

        $final = $event->bracketRounds()->where('round_number', 2)->firstOrFail()->matches->first();

        $this->assertSame($winner->id, $final->home_club_id);
        $this->assertSame($winner->id, $semifinal->fresh()->winner_club_id);
    }

    public function test_winner_must_be_one_of_the_playing_clubs(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $outsider = $this->clubs(1, 'X')[0];
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $semifinal = $this->firstRound($event)->first();

        $this->patch(route('admin.events.bracket.winner', [$event, $semifinal]), ['winner_club_id' => $outsider->id])
            ->assertSessionHasErrors('winner_club_id');

        $this->assertNull($semifinal->fresh()->winner_club_id);
    }

    public function test_deleting_the_bracket_keeps_participants_but_clears_seeds(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $this->delete(route('admin.events.bracket.destroy', $event))->assertRedirect();

        $this->assertDatabaseCount('event_bracket_matches', 0);
        $this->assertDatabaseCount('event_bracket_rounds', 0);
        $this->assertDatabaseCount('event_participants', 4);
        $this->assertSame(0, $event->participants()->whereNotNull('seed')->count());
    }

    public function test_participant_page_shows_the_generated_bracket(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(4);
        $this->actAsAdmin();

        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);
        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)]);

        $this->get(route('admin.events.teams.index', $event))
            ->assertOk()
            ->assertSee('Semifinal')
            ->assertSee('Final')
            ->assertSee($clubs[0]->name)
            ->assertSee('Simpan Semua Tim pada Bagan')
            ->assertSee('Hapus Bagan')
            ->assertDontSee('Acak Undian');
    }

    public function test_bracket_capacity_is_enforced_when_selecting_more_clubs_than_slots(): void
    {
        $event = $this->event('knockout');
        $clubs = $this->clubs(8);
        $this->actAsAdmin();

        // Bagan 4 slot, tapi 8 klub dipilih sekaligus
        $this->post(route('admin.events.bracket.generate', $event), ['team_count' => 4]);

        $this->post(route('admin.events.participants.store', $event), ['club_ids' => $this->clubIds($clubs)])
            ->assertSessionHasErrors('club_ids');

        $this->assertDatabaseCount('event_participants', 0);
    }

    public function test_bracket_size_and_round_names(): void
    {
        $service = app(BracketService::class);

        $this->assertSame(4, $service->bracketSize(3));
        $this->assertSame(8, $service->bracketSize(6));
        $this->assertSame(16, $service->bracketSize(16));

        $this->assertSame('Final', $service->roundName(2, 1));
        $this->assertSame('Final', $service->roundName(4, 2));
        $this->assertSame('Semifinal', $service->roundName(8, 2));
        $this->assertSame('Perempat Final', $service->roundName(8, 1));
    }

    public function test_participant_pages_are_protected_from_non_admin_users(): void
    {
        $event = $this->event();
        $role = Role::create(['name' => 'Admin Klub', 'slug' => 'club-admin']);

        $this->actingAs(User::create([
            'name' => 'Club Admin',
            'email' => 'club@example.com',
            'role_id' => $role->id,
            'password' => 'password',
        ]));

        $this->get(route('admin.events.teams.index', $event))->assertForbidden();
    }
}
