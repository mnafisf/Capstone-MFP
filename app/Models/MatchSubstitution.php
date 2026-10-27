<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchSubstitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'club_id',
        'minute_label',
        'player_out_id',
        'player_in_id',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function playerOut(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_out_id');
    }

    public function playerIn(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_in_id');
    }
}