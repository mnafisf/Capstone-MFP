<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionSetting extends Model
{
    use HasFactory;

    protected $fillable = ['lineup_deadline'];

    protected function casts(): array
    {
        return [
            'lineup_deadline' => 'datetime',
        ];
    }
}
