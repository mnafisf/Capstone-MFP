<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'from_club_id',
        'to_club_id',
        'status',
        'transfer_state',
        'document_path',
        'verification_status',
        'verification_note',
        'verified_at',
        'released_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'released_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function fromClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'from_club_id');
    }

    public function toClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'to_club_id');
    }
}
