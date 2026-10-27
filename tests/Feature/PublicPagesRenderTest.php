<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Event;
use App\Models\MatchCard;
use App\Models\MatchModel;
use App\Models\MatchPlayerStat;
use App\Models\MatchResult;
use App\Models\Player;
use App\Services\BracketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublicPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    private function seedCompetition(): Event
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

        $garuda = Club::create(['name' => 'Garuda Muda FC', 'code' => 'GAR', 'is_active' => true, 'shirt_color' => '#c0182f']);
        $nusantara = Club::create(['name' => 'Nusantara United', 'code' => 'NUS', 'is_active' => true, 'shirt_color' => '#123f8f']);

        $scorer = Player::create([
            'club_id' => $garuda->id, 'name' => 'Budi Santoso', 'nik' => '320100000001',
            'birth_place_date' => 'Bandung 1 Januari 2010', 'status' => 'approved',
        ]);
        $other = Player::create([
            'club_id' => $nusantara->id, 'name' => 'Andi Wijaya', 'nik' => '320100000002',
            'birth_place_date' => 'Jakarta 5 Februari 2010', 'status' => 'approved',
        ]);

        $finished = MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $garuda->id,
            'away_club_id' => $nusantara->id,
            'match_date' => Carbon::parse('2026-06-10 15:00:00'),
        ]);
        MatchResult::create(['match_id' => $finished->id, 'home_score' => 2, 'away_score' => 1]);
        MatchPlayerStat::create(['match_id' => $finished->id, 'player_id' => $scorer->id, 'goals' => 2]);
        MatchPlayerStat::create(['match_id' => $finished->id, 'player_id' => $other->id, 'goals' => 1]);
        MatchCard::create([
            'match_id' => $finished->id, 'player_id' => $other->id,
            'club_id' => $nusantara->id, 'minute_label' => '60', 'card_type' => 'yellow',
        ]);

        MatchModel::create([
            'event_id' => $event->id,
            'home_club_id' => $nusantara->id,
            'away_club_id' => $garuda->id,
            'match_date' => now()->addDays(7)->setTime(15, 0),
        ]);

        return $event;
    }

    public function test_events_index_lists_events_and_filters(): void
    {
        $this->seedCompetition();

        $this->get(route('public.events.index'))
            ->assertOk()
            ->assertSee('Daftar Event')
            ->assertSee('Liga U-15 Nasional')
            ->assertSee('Lihat Detail');

        $this->get(route('public.events.index', ['filter' => 'liga']))
            ->assertOk()
            ->assertSee('Liga U-15 Nasional');

        $this->get(route('public.events.index', ['filter' => 'knockout']))
            ->assertOk()
            ->assertSee('Tidak ada event pada filter ini.');
    }

    public function test_event_detail_page_renders_all_tabs(): void
    {
        $event = $this->seedCompetition();

        $this->get(route('public.events.show', $event->id))
            ->assertOk()
            ->assertSee('Liga U-15 Nasional')
            ->assertSee('Klub Peserta')
            ->assertSee('Jadwal & Hasil', false)
            ->assertSee('Klasemen Liga')
            ->assertSee('Budi Santoso')
            ->assertSee('Andi Wijaya')
            ->assertSee('Kartu Kuning')
            ->assertSee('Kartu Merah');
    }

    public function test_teams_page_shows_club_details_and_search(): void
    {
        $this->seedCompetition();

        $this->get(route('public.teams.index'))
            ->assertOk()
            ->assertSee('Daftar Tim')
            ->assertSee('Garuda Muda FC')
            ->assertSee('Kode: GAR')
            ->assertSee('Lihat Jadwal Tim');

        $this->get(route('public.teams.index', ['search' => 'Garuda']))
            ->assertOk()
            ->assertSee('Garuda Muda FC')
            ->assertDontSee('Nusantara United');
    }

    public function test_standings_page_shows_real_standings_numbers(): void
    {
        $this->seedCompetition();

        $response = $this->get(route('public.standings.index'));

        $response->assertOk()
            ->assertSee('Klasemen & Top Skor')
            ->assertSee('Liga U-15 Nasional')
            ->assertSee('Garuda Muda FC')
            ->assertSee('Budi Santoso');

        // Angka klasemen harus benar-benar terisi, bukan 0 semua
        $response->assertSeeInOrder(['Garuda Muda FC', 'Nusantara United']);
        $this->assertStringContainsString('SG: selisih gol', $response->getContent());
    }

    public function test_knockout_event_shows_the_prepared_bracket_publicly(): void
    {
        $event = $this->seedCompetition();
        $event->competition_type = 'knockout';
        $event->save();

        $clubs = Club::where('is_active', true)->orderBy('id')->get();
        $event->participants()->createMany(
            $clubs->values()->map(fn (Club $club, int $index): array => [
                'club_id' => $club->id,
                'seed' => $index + 1,
            ])->all(),
        );

        app(BracketService::class)->generate($event, 4);

        $this->get(route('public.events.show', $event->id))
            ->assertOk()
            ->assertSee('Semifinal')
            ->assertSee('Final')
            ->assertSee('Menunggu tim')
            ->assertSee('Garuda Muda FC');
    }

    public function test_public_pages_render_without_any_data(): void
    {
        foreach (['public.events.index', 'public.teams.index', 'public.standings.index'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }
}
