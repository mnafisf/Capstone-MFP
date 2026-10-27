<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Transfer;
use App\Repositories\TransferRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferService
{
    public function __construct(private readonly TransferRepository $transferRepository)
    {
    }

    public function request(Player $player, int $toClubId, ?string $documentPath = null): Transfer
    {
        return $this->transferRepository->create([
            'player_id' => $player->id,
            'from_club_id' => $player->club_id,
            'to_club_id' => $toClubId,
            'status' => 'available',
            'transfer_state' => 'requested',
            'document_path' => $documentPath,
            'verification_status' => 'pending',
            'verification_note' => null,
            'verified_at' => null,
        ]);
    }

    public function approveBySuperAdmin(Transfer $transfer, ?string $verificationNote = null): Transfer
    {
        if ($transfer->verification_status !== 'pending') {
            throw ValidationException::withMessages(['transfer' => 'Transfer sudah diproses sebelumnya.']);
        }

        DB::transaction(function () use ($transfer, $verificationNote) {
            $transfer->player->update(['club_id' => $transfer->to_club_id]);

            $transfer->update([
                'verification_status' => 'approved',
                'verification_note' => $verificationNote,
                'verified_at' => Carbon::now(),
                'transfer_state' => 'accepted',
                'accepted_at' => Carbon::now(),
                'status' => 'non-available',
            ]);
        });

        return $transfer->refresh();
    }

    public function rejectBySuperAdmin(Transfer $transfer, ?string $verificationNote = null): Transfer
    {
        if ($transfer->verification_status !== 'pending') {
            throw ValidationException::withMessages(['transfer' => 'Transfer sudah diproses sebelumnya.']);
        }

        $transfer->update([
            'verification_status' => 'rejected',
            'verification_note' => $verificationNote,
            'verified_at' => Carbon::now(),
        ]);

        return $transfer->refresh();
    }
}
