<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Official extends Model
{
    use HasFactory;

    public const POSITIONS = [
        'pelatih' => 'Pelatih',
        'asisten_pelatih' => 'Asisten Pelatih',
        'manager' => 'Manager',
        'medis' => 'Medis',
    ];

    protected $fillable = [
        'club_id',
        'name',
        'position',
        'phone',
        'email',
        'status',
        'verification_note',
    ];

    protected $appends = ['position_label'];

    protected function positionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::POSITIONS[$this->position] ?? $this->position,
        );
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
