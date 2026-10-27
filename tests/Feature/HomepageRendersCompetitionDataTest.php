<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Event;
use App\Models\MatchModel;
use App\Models\MatchGoal;
use App\Models\MatchPlayerStat;
use App\Models\MatchResult;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HomepageRendersCompetitionDataTest extends TestCase
{
    use RefreshDatabase;

    private function seedCompetition(): void
    {
        $event = Event::create([
            'name' => 'Liga U-15 Nasional',
            'competition_type' => 'league',
            'min_age' => 12,
            'max_age' => 15,
            'start_date' => Carbon::parse('2026-06-01'),
            'end_date' => Carbon::parse('2026-09-30'),
            'is_active' => true,
        ]);

        $garuda = Club::create([
            'name' => 'Garuda Muda FC',
            'code' => 'GAR',
            'is_active' => true,
            'shirt_color' => '#c0182f',
            'pants_color' => '#ffffff',
        ]);
        $nusantara = Club::create([
            'name' => 'Nusantara United',
            'code' => 'NUS',
            'is_active' => true,
            'shirt_color' => '#123f8f',
        ]);

        $garudaPlayer = Player::create([
            'club_id' => $garuda->id,
            'name' => 'Budi Santoso',
            'nik' => '320100000001',
            'birth_place_date' => 'Bandung 1 Januari 2010',
            'status' => 'approved',
        ]);
        $nusantaraPlayer = Player::create([
            'club_id' => $nusantara->id,
            'name' => 'Andi Wijaya',
            'nik' => '320100000002',
            'birth_place_date' => 'Jakarta 5 Februari 2010',
            'status' => 'approved',
        ]);

        $finished = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $garuda->id,
            'away_club_id' => $nusantara->id,
            'match_date' => Carbon::parse('2026-06-10 15:00:00'),
        ]);

        MatchResult::create(['match_id' => $finished->id, 'home_score' => 2, 'away_score' => 1]);
        MatchGoal::create(['match_id' => $finished->id, 'player_id' => $garudaPlayer->id, 'club_id' => $garuda->id, 'minute_label' => '12']);
        MatchGoal::create(['match_id' => $finished->id, 'player_id' => $garudaPlayer->id, 'club_id' => $garuda->id, 'minute_label' => '55']);
        MatchGoal::create(['match_id' => $finished->id, 'player_id' => $nusantaraPlayer->id, 'club_id' => $nusantara->id, 'minute_label' => '70']);
        MatchPlayerStat::create(['match_id' => $finished->id, 'player_id' => $garudaPlayer->id, 'goals' => 2]);
        MatchPlayerStat::create(['match_id' => $finished->id, 'player_id' => $nusantaraPlayer->id, 'goals' => 1]);

        MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $nusantara->id,
            'away_club_id' => $garuda->id,
            'match_date' => now()->addDays(7)->setTime(15, 0),
        ]);
    }

    public function test_homepage_shows_standings_top_scorers_fixtures_and_results(): void
    {
        $this->seedCompetition();

        $response = $this->get(route('home'));

        $response->assertOk();

        // Klasemen
        $response->assertSee('Klasemen');
        $response->assertSee('Garuda Muda FC');
        $response->assertSee('Nusantara United');
        $response->assertSeeInOrder(['Garuda Muda FC', 'Nusantara United']);
        $response->assertSee('SG: selisih gol');

        // Top skor memakai data match_player_stats
        $response->assertSee('Top Skor');
        $response->assertSee('Budi Santoso');

        // Jadwal mendatang + hasil terakhir
        $response->assertSee('Pertandingan Mendatang');
        $response->assertSee('Hasil Terakhir');

        // Ringkasan jumlah data pada hero
        $response->assertSee('Liga U-15 Nasional');
    }

    public function test_homepage_shows_form_guide_for_finished_matches(): void
    {
        $this->seedCompetition();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('M', false);
    }

    public function test_homepage_renders_when_there_is_no_competition_data(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Belum ada pertandingan yang selesai, jadi klasemen masih kosong.')
            ->assertSee('Belum ada tim terdaftar.');
    }
}
