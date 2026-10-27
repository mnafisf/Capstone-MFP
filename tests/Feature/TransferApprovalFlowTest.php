<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Player;
use App\Models\Role;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TransferApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $role = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);

        return User::firstOrCreate(
            ['email' => 'super@example.com'],
            ['name' => 'Super Admin', 'role_id' => $role->id, 'password' => 'password'],
        );
    }

    private function pendingTransfer(): Transfer
    {
        $fromClub = Club::create(['name' => 'Club A', 'code' => 'A', 'is_active' => true]);
        $toClub = Club::create(['name' => 'Club B', 'code' => 'B', 'is_active' => true]);

        $player = Player::create([
            'club_id' => $fromClub->id,
            'name' => 'Player One',
            'nik' => '320100000001',
            'status' => 'approved',
        ]);

        return Transfer::create([
            'player_id' => $player->id,
            'from_club_id' => $fromClub->id,
            'to_club_id' => $toClub->id,
            'status' => 'available',
            'transfer_state' => 'requested',
            'verification_status' => 'pending',
        ]);
    }

    public function test_approving_a_transfer_moves_the_player_to_the_destination_club(): void
    {
        $transfer = $this->pendingTransfer();

        $this->actingAs($this->superAdmin())
            ->patch(route('super-admin.transfers.verification.update', $transfer), [
                'status' => 'approved',
                'verification_note' => 'Dokumen sah.',
            ])
            ->assertRedirect();

        $transfer->refresh();

        $this->assertSame('approved', $transfer->verification_status);
        $this->assertSame('accepted', $transfer->transfer_state);
        $this->assertSame('non-available', $transfer->status);
        $this->assertNotNull($transfer->accepted_at);
        $this->assertSame($transfer->to_club_id, $transfer->player->fresh()->club_id);
    }

    public function test_rejecting_a_transfer_keeps_the_player_in_the_source_club(): void
    {
        $transfer = $this->pendingTransfer();
        $fromClubId = $transfer->from_club_id;

        $this->actingAs($this->superAdmin())
            ->patch(route('super-admin.transfers.verification.update', $transfer), [
                'status' => 'rejected',
                'verification_note' => 'Dokumen tidak lengkap.',
            ])
            ->assertRedirect();

        $transfer->refresh();

        $this->assertSame('rejected', $transfer->verification_status);
        $this->assertSame('requested', $transfer->transfer_state);
        $this->assertSame($fromClubId, $transfer->player->fresh()->club_id);
    }

    public function test_an_approved_transfer_cannot_be_approved_twice(): void
    {
        $transfer = $this->pendingTransfer();
        $superAdmin = $this->superAdmin();

        $this->actingAs($superAdmin)
            ->patch(route('super-admin.transfers.verification.update', $transfer), ['status' => 'approved'])
            ->assertRedirect();

        $this->actingAs($superAdmin)
            ->from(route('super-admin.transfers.verification.index'))
            ->patch(route('super-admin.transfers.verification.update', $transfer), ['status' => 'approved'])
            ->assertSessionHasErrors();

        $this->assertSame('approved', $transfer->fresh()->verification_status);
    }

    public function test_club_admin_transfer_release_and_accept_routes_no_longer_exist(): void
    {
        $this->assertFalse(Route::has('club-admin.transfers.release'));
        $this->assertFalse(Route::has('club-admin.transfers.accept'));
    }
}
