<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lineup extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'club_id',
        'shirt_color',
        'pants_color',
        'socks_color',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function lineupPlayers(): HasMany
    {
        return $this->hasMany(LineupPlayer::class);
    }
}
