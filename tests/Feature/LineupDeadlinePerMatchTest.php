<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Event;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LineupDeadlinePerMatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_sets_deadline_for_selected_match(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-29 10:00:00'));

        $superAdminRole = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'role_id' => $superAdminRole->id,
            'password' => 'password',
        ]);

        $event = Event::create([
            'name' => 'Liga U-15',
            'competition_type' => 'league',
        ]);

        $homeClub = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $awayClub = Club::create(['name' => 'Club B', 'code' => 'B', 'is_active' => true]);

        $match = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $homeClub->id,
            'away_club_id' => $awayClub->id,
            'match_date' => Carbon::parse('2026-06-02 15:00:00'),
        ]);

        $deadline = Carbon::parse('2026-06-01 18:00:00');

        $this->actingAs($superAdmin)
            ->put(route('super-admin.settings.update', $match), [
                'lineup_deadline' => $deadline->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('super-admin.settings.edit', $match));

        $this->assertTrue($match->fresh()->lineup_deadline?->equalTo($deadline));
    }

    public function test_club_admin_can_only_store_lineup_before_the_match_deadline(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-29 10:00:00'));

        $clubAdminRole = Role::create(['name' => 'Club Admin', 'slug' => 'club-admin']);
        $club = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $otherClub = Club::create(['name' => 'Club B', 'code' => 'B', 'is_active' => true]);

        $clubAdmin = User::create([
            'name' => 'Club Admin',
            'email' => 'club@example.com',
            'role_id' => $clubAdminRole->id,
            'club_id' => $club->id,
            'password' => 'password',
        ]);

        $event = Event::create([
            'name' => 'Liga U-15',
            'competition_type' => 'league',
        ]);

        $futureMatch = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $club->id,
            'away_club_id' => $otherClub->id,
            'match_date' => Carbon::parse('2026-06-05 15:00:00'),
            'lineup_deadline' => Carbon::parse('2026-06-04 18:00:00'),
        ]);

        $closedMatch = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $club->id,
            'away_club_id' => $otherClub->id,
            'match_date' => Carbon::parse('2026-06-06 15:00:00'),
            'lineup_deadline' => Carbon::parse('2026-05-28 18:00:00'),
        ]);

        $player = Player::create([
            'club_id' => $club->id,
            'name' => 'Player One',
            'nik' => '1234567890123456',
            'birth_place_date' => 'Bandung 1 Januari 2010',
            'status' => 'approved',
        ]);

        $this->actingAs($clubAdmin)
            ->post(route('club-admin.lineups.store'), [
                'match_id' => $futureMatch->id,
                'player_statuses' => [$player->id => 'starter'],
            ])
            ->assertRedirect(route('club-admin.lineups.index'));

        $this->assertDatabaseHas('lineups', [
            'match_id' => $futureMatch->id,
            'club_id' => $club->id,
        ]);

        $this->actingAs($clubAdmin)
            ->post(route('club-admin.lineups.store'), [
                'match_id' => $closedMatch->id,
                'player_statuses' => [$player->id => 'starter'],
            ])
            ->assertSessionHasErrors('deadline');

        $this->assertDatabaseMissing('lineups', [
            'match_id' => $closedMatch->id,
            'club_id' => $club->id,
        ]);
    }

    public function test_settings_index_can_filter_matches_by_event(): void
    {
        $superAdminRole = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'role_id' => $superAdminRole->id,
            'password' => 'password',
        ]);

        $eventOne = Event::create([
            'name' => 'Liga A',
            'competition_type' => 'league',
        ]);

        $eventTwo = Event::create([
            'name' => 'Liga B',
            'competition_type' => 'league',
        ]);

        $clubOne = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $clubTwo = Club::create(['name' => 'Club B', 'code' => 'B', 'is_active' => true]);
        $clubThree = Club::create(['name' => 'Club C', 'code' => 'C', 'is_active' => true]);
        $clubFour = Club::create(['name' => 'Club D', 'code' => 'D', 'is_active' => true]);

        MatchModel::create([
            'event_id' => $eventOne->id,
            'home_club_id' => $clubOne->id,
            'away_club_id' => $clubTwo->id,
            'match_date' => Carbon::parse('2026-06-02 15:00:00'),
        ]);

        MatchModel::create([
            'event_id' => $eventTwo->id,
            'home_club_id' => $clubThree->id,
            'away_club_id' => $clubFour->id,
            'match_date' => Carbon::parse('2026-06-03 15:00:00'),
        ]);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.settings.index', ['event_id' => $eventTwo->id]))
            ->assertOk()
            ->assertSeeText('Club C vs Club D')
            ->assertSeeText('Liga B')
            ->assertDontSeeText('Club A vs Club B');
    }
}