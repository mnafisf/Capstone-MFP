<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Event;
use App\Models\MatchCard;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Role;
use App\Models\User;
use App\Services\PlayerDisciplineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlayerCardDisciplineTest extends TestCase
{
    use RefreshDatabase;

    private function createClubAdmin(Club $club): User
    {
        $role = Role::create(['name' => 'Club Admin', 'slug' => 'club-admin']);

        return User::create([
            'name' => 'Club Admin',
            'email' => 'club-'.$club->id.'@example.com',
            'role_id' => $role->id,
            'club_id' => $club->id,
            'password' => 'password',
        ]);
    }

    public function test_player_is_suspended_for_next_match_after_yellow_card_threshold(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00'));

        $event = Event::create([
            'name' => 'Liga U-15',
            'competition_type' => 'league',
            'yellow_card_threshold' => 3,
            'yellow_card_ban_matches' => 1,
            'red_card_ban_matches' => 1,
        ]);

        $club = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $opponent = Club::create(['name' => 'Club B', 'code' => 'B', 'is_active' => true]);

        $player = Player::create([
            'club_id' => $club->id,
            'name' => 'Player One',
            'nik' => '1234567890123456',
            'birth_place_date' => 'Bandung 1 Januari 2010',
            'status' => 'approved',
        ]);

        $matchOne = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $club->id,
            'away_club_id' => $opponent->id,
            'match_date' => Carbon::parse('2026-06-02 15:00:00'),
        ]);

        $matchTwo = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $opponent->id,
            'away_club_id' => $club->id,
            'match_date' => Carbon::parse('2026-06-09 15:00:00'),
            'lineup_deadline' => Carbon::parse('2026-06-08 18:00:00'),
        ]);

        $matchThree = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $club->id,
            'away_club_id' => $opponent->id,
            'match_date' => Carbon::parse('2026-06-16 15:00:00'),
            'lineup_deadline' => Carbon::parse('2026-06-15 18:00:00'),
        ]);

        foreach (range(1, 3) as $index) {
            MatchCard::create([
                'match_id' => $matchOne->id,
                'player_id' => $player->id,
                'club_id' => $club->id,
                'minute_label' => (string) ($index * 10),
                'card_type' => 'yellow',
            ]);
        }

        $service = app(PlayerDisciplineService::class);

        $suspendedStatus = $service->getStatusForMatch($player->id, $matchTwo, $club->id);
        $this->assertTrue($suspendedStatus['is_suspended']);
        $this->assertSame('akumulasi kartu kuning', $suspendedStatus['suspension_reason']);

        $availableStatus = $service->getStatusForMatch($player->id, $matchThree, $club->id);
        $this->assertFalse($availableStatus['is_suspended']);
        $this->assertSame(0, $availableStatus['yellow_count']);

        $clubAdmin = $this->createClubAdmin($club);

        $this->actingAs($clubAdmin)
            ->post(route('club-admin.lineups.store'), [
                'match_id' => $matchTwo->id,
                'starters' => [$player->id],
            ])
            ->assertSessionHasErrors('starters');
    }

    public function test_player_is_suspended_for_next_match_after_red_card(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00'));

        $event = Event::create([
            'name' => 'Liga U-15',
            'competition_type' => 'league',
            'yellow_card_threshold' => 3,
            'yellow_card_ban_matches' => 1,
            'red_card_ban_matches' => 1,
        ]);

        $club = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $opponent = Club::create(['name' => 'Club B', 'code' => 'B', 'is_active' => true]);

        $player = Player::create([
            'club_id' => $club->id,
            'name' => 'Player Red',
            'nik' => '2234567890123456',
            'birth_place_date' => 'Bandung 1 Januari 2010',
            'status' => 'approved',
        ]);

        $matchOne = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $club->id,
            'away_club_id' => $opponent->id,
            'match_date' => Carbon::parse('2026-06-02 15:00:00'),
        ]);

        $matchTwo = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $club->id,
            'away_club_id' => $opponent->id,
            'match_date' => Carbon::parse('2026-06-09 15:00:00'),
            'lineup_deadline' => Carbon::parse('2026-06-08 18:00:00'),
        ]);

        MatchCard::create([
            'match_id' => $matchOne->id,
            'player_id' => $player->id,
            'club_id' => $club->id,
            'minute_label' => '55',
            'card_type' => 'red',
        ]);

        $service = app(PlayerDisciplineService::class);
        $status = $service->getStatusForMatch($player->id, $matchTwo, $club->id);

        $this->assertTrue($status['is_suspended']);
        $this->assertSame('kartu merah', $status['suspension_reason']);
    }
}
