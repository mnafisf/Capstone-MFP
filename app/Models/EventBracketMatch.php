<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBracketMatch extends Model
{
    use HasFactory;

    protected $table = 'event_bracket_matches';

    protected $fillable = [
        'event_bracket_round_id',
        'position',
        'home_club_id',
        'away_club_id',
        'winner_club_id',
        'winner_to_match_id',
        'winner_to_position',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'winner_to_position' => 'integer',
        ];
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(EventBracketRound::class, 'event_bracket_round_id');
    }

    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }

    public function winnerClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'winner_club_id');
    }

    public function winnerToMatch(): BelongsTo
    {
        return $this->belongsTo(self::class, 'winner_to_match_id');
    }

    public function scheduledMatch(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MatchModel::class, 'event_bracket_match_id');
    }

    public function hasBothTeams(): bool
    {
        return $this->home_club_id !== null && $this->away_club_id !== null;
    }

    public function isDecided(): bool
    {
        return $this->winner_club_id !== null;
    }

    public function statusLabel(): string
    {
        if ($this->isDecided()) {
            return 'Selesai';
        }

        return $this->hasBothTeams() ? 'Menunggu hasil' : 'Menunggu tim';
    }
}

