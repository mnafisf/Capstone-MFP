<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Event;
use App\Models\MatchModel;
use App\Models\MatchResult;
use App\Models\Player;
use App\Models\Referee;
use App\Models\Stadium;
use Illuminate\Database\Seeder;

class CompetitionDataSeeder extends Seeder
{
    public function run(): void
    {
        $stadium = Stadium::updateOrCreate(
            ['name' => 'Stadion Merdeka'],
            ['city' => 'Bandung', 'address' => 'Jl. Stadion No. 1', 'is_active' => true]
        );

        $referee = Referee::updateOrCreate(
            ['name' => 'Rudi Hartono'],
            ['license_number' => 'REF-001', 'phone' => '08123456789']
        );

        $event = Event::updateOrCreate(
            ['name' => 'Liga U-15 Nasional'],
            [
                'competition_type' => 'league',
                'min_age' => 12,
                'max_age' => 15,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(3)->toDateString(),
            ]
        );

        $clubs = Club::query()->take(2)->get();
        if ($clubs->count() < 2) {
            return;
        }

        $playerA = Player::query()
            ->where('club_id', $clubs[0]->id)
            ->where('status', 'approved')
            ->orderBy('jersey_number')
            ->first();

        $playerB = Player::query()
            ->where('club_id', $clubs[1]->id)
            ->where('status', 'approved')
            ->orderBy('jersey_number')
            ->first();

        if (! $playerA || ! $playerB) {
            return;
        }

        $match = MatchModel::updateOrCreate(
            [
                'event_id' => $event->id,
                'home_club_id' => $clubs[0]->id,
                'away_club_id' => $clubs[1]->id,
                'match_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            ],
            [
                'stadium_id' => $stadium->id,
                'referee_id' => $referee->id,
                'lineup_deadline' => now()->addDays(2),
                'summary' => 'Pertandingan pembuka kompetisi',
            ]
        );

        MatchResult::updateOrCreate(
            ['match_id' => $match->id],
            ['home_score' => 2, 'away_score' => 1]
        );

        $match->playerStats()->updateOrCreate(['player_id' => $playerA->id], ['goals' => 2]);
        $match->playerStats()->updateOrCreate(['player_id' => $playerB->id], ['goals' => 1]);
    }
}
