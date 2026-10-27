<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'competition_type',
        'is_active',
        'min_age',
        'max_age',
        'start_date',
        'end_date',
        'yellow_card_threshold',
        'yellow_card_ban_matches',
        'red_card_ban_matches',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchModel::class, 'event_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function bracketRounds(): HasMany
    {
        return $this->hasMany(EventBracketRound::class)->orderBy('round_number');
    }

    public function isKnockout(): bool
    {
        return $this->competition_type === 'knockout';
    }

    public function hasBracket(): bool
    {
        return $this->bracketRounds()->exists();
    }

    public function isAgeAllowed(int $age): bool
    {
        if (! is_null($this->min_age) && $age < $this->min_age) {
            return false;
        }

        if (! is_null($this->max_age) && $age > $this->max_age) {
            return false;
        }

        return true;
    }
}
