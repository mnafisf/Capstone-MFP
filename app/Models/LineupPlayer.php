<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineupPlayer extends Model
{
    use HasFactory;

    protected $fillable = ['lineup_id', 'player_id', 'position_type', 'field_position', 'is_captain', 'shirt_number'];

    public function lineup(): BelongsTo
    {
        return $this->belongsTo(Lineup::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
