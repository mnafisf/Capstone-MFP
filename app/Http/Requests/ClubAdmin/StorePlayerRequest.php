<?php

namespace App\Http\Requests\ClubAdmin;

use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $playerId = $this->route('player')?->id;
        $isDraft = $this->input('submit_action') === 'draft';

        return [
            'submit_action' => ['nullable', Rule::in(['draft', 'final'])],
            'name' => [$isDraft ? 'nullable' : 'required', 'string', 'max:255'],
            'popular_name' => ['nullable', 'string', 'max:255'],
            'birth_place_date' => [
                $isDraft ? 'nullable' : 'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($isDraft): void {
                    if ($isDraft || ! filled($value)) {
                        return;
                    }

                    if (! Player::parseBirthDateFromString($value)) {
                        $fail('Format tempat tanggal lahir harus seperti: Bantul 06 Juni 2006.');
                    }
                },
            ],
            'jersey_number' => [
                'nullable',
                'integer',
                'between:1,99',
                Rule::unique('players', 'jersey_number')
                    ->where(fn ($query) => $query->where('club_id', $this->user()->club_id))
                    ->ignore($playerId),
            ],
            'age_group' => [$isDraft ? 'nullable' : 'required', 'integer', Rule::in([10, 12, 15, 17])],
            'gender' => [$isDraft ? 'nullable' : 'required', Rule::in(['laki-laki', 'perempuan'])],
            'country' => ['nullable', 'string', 'max:100'],
            'dominant_foot' => [$isDraft ? 'nullable' : 'required', Rule::in(['kanan', 'kiri'])],
            'height_cm' => ['nullable', 'numeric', 'min:50', 'max:250'],
            'weight_kg' => ['nullable', 'numeric', 'min:10', 'max:200'],
            'address' => ['nullable', 'string', 'max:500'],
            'province' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('players', 'email')->ignore($playerId)],
            'nik' => [$isDraft ? 'nullable' : 'required', 'string', 'max:50', Rule::unique('players', 'nik')->ignore($playerId)],

            'photo' => ['nullable', 'file', 'mimes:pdf,png,jpg', 'max:1024'],
            'family_card' => ['nullable', 'file', 'mimes:pdf,png,jpg', 'max:1024'],
            'club_letter' => ['nullable', 'file', 'mimes:pdf,png,jpg', 'max:1024'],
            'birth_certificate' => ['nullable', 'file', 'mimes:pdf,png,jpg', 'max:1024'],
            'report_card' => ['nullable', 'file', 'mimes:pdf,png,jpg', 'max:1024'],
            'privacy_consent_letter' => ['nullable', 'file', 'mimes:pdf,png,jpg', 'max:1024'],
        ];
    }

    public function attributes(): array
    {
        return [
            'birth_place_date' => 'tempat tanggal lahir',
        ];
    }
}
