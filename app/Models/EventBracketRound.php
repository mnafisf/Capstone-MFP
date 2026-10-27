<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventBracketRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'round_number',
        'name',
        'match_count',
    ];

    protected function casts(): array
    {
        return [
            'round_number' => 'integer',
            'match_count' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(EventBracketMatch::class, 'event_bracket_round_id')
            ->orderBy('position');
    }
}
