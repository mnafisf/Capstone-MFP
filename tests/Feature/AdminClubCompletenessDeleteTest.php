<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Official;
use App\Models\Player;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClubCompletenessDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::create(['name' => 'Admin Panitia', 'slug' => 'admin']);

        return User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role_id' => $role->id,
            'password' => 'password',
        ]);
    }

    public function test_admin_can_delete_a_club_player_and_is_redirected_to_the_club_page(): void
    {
        $club = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $player = Player::create([
            'club_id' => $club->id,
            'name' => 'Player One',
            'nik' => '320100000001',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.club-completeness.players.destroy', [$club, $player]))
            ->assertRedirect(route('admin.club-completeness.show', $club))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('players', ['id' => $player->id]);
    }

    public function test_admin_can_delete_a_club_official_and_is_redirected_to_the_club_page(): void
    {
        $club = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $official = Official::create([
            'club_id' => $club->id,
            'name' => 'Pelatih Satu',
            'position' => 'pelatih',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.club-completeness.officials.destroy', [$club, $official]))
            ->assertRedirect(route('admin.club-completeness.show', $club))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('officials', ['id' => $official->id]);
    }

    public function test_admin_cannot_delete_a_player_from_another_club(): void
    {
        $club = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $otherClub = Club::create(['name' => 'Club B', 'code' => 'B', 'is_active' => true]);
        $player = Player::create([
            'club_id' => $otherClub->id,
            'name' => 'Player Two',
            'nik' => '320100000002',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.club-completeness.players.destroy', [$club, $player]))
            ->assertNotFound();

        $this->assertDatabaseHas('players', ['id' => $player->id]);
    }
}
