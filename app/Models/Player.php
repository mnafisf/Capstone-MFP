<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    use HasFactory;

    private const INDONESIAN_MONTHS = [
        'januari' => 1,
        'februari' => 2,
        'maret' => 3,
        'april' => 4,
        'mei' => 5,
        'juni' => 6,
        'juli' => 7,
        'agustus' => 8,
        'september' => 9,
        'oktober' => 10,
        'november' => 11,
        'desember' => 12,
    ];

    protected $fillable = [
        'club_id',
        'name',
        'popular_name',
        'nik',
        'birth_place_date',
        'jersey_number',
        'age_group',
        'gender',
        'country',
        'dominant_foot',
        'height_cm',
        'weight_kg',
        'address',
        'province',
        'phone',
        'email',
        'photo_path',
        'family_card_path',
        'club_letter_path',
        'birth_certificate_path',
        'report_card_path',
        'privacy_consent_letter_path',
        'document_path',
        'status',
        'verification_note',
    ];

    protected $appends = ['age'];

    protected function casts(): array
    {
        return [
            'height_cm' => 'float',
            'weight_kg' => 'float',
        ];
    }

    public static function parseBirthDateFromString(?string $value): ?Carbon
    {
        if (! filled($value)) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/(\d{1,2})\s+(\p{L}+)\s+(\d{4})\s*$/u', $value, $matches)) {
            $month = self::INDONESIAN_MONTHS[strtolower($matches[2])] ?? null;

            if ($month) {
                return Carbon::createFromDate((int) $matches[3], $month, (int) $matches[1])->startOfDay();
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => self::parseBirthDateFromString($this->birth_place_date)?->age,
        );
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function transferHistories(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }
}
