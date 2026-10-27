<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchModel extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'event_id',
        'event_bracket_match_id',
        'home_club_id',
        'away_club_id',
        'stadium_id',
        'referee_id',
        'referee_2_id',
        'referee_3_id',
        'match_date',
        'lineup_deadline',
        'temperature',
        'spectators',
        'summary',
        'supervisor_note',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
            'lineup_deadline' => 'datetime',
        ];
    }

    public function canManageLineups(): bool
    {
        return $this->match_date->isFuture() && $this->lineup_deadline && now()->lte($this->lineup_deadline);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function eventBracketMatch(): BelongsTo
    {
        return $this->belongsTo(EventBracketMatch::class, 'event_bracket_match_id');
    }

    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class);
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class);
    }

    public function referee2(): BelongsTo
    {
        return $this->belongsTo(Referee::class, 'referee_2_id');
    }

    public function referee3(): BelongsTo
    {
        return $this->belongsTo(Referee::class, 'referee_3_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(MatchResult::class, 'match_id');
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(Lineup::class, 'match_id');
    }

    public function playerStats(): HasMany
    {
        return $this->hasMany(MatchPlayerStat::class, 'match_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(MatchGoal::class, 'match_id')->orderBy('id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(MatchCard::class, 'match_id')->orderBy('id');
    }

    public function substitutions(): HasMany
    {
        return $this->hasMany(MatchSubstitution::class, 'match_id')->orderBy('id');
    }
}
