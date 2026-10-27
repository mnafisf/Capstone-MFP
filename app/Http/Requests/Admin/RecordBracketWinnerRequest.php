<?php

namespace App\Http\Requests\Admin;

use App\Models\EventBracketMatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RecordBracketWinnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'winner_club_id' => ['required', 'integer', 'exists:clubs,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var EventBracketMatch $match */
            $match = $this->route('match');

            $teams = [(int) $match->home_club_id, (int) $match->away_club_id];

            if ($match->home_club_id === null || $match->away_club_id === null) {
                $validator->errors()->add('winner_club_id', 'Pertandingan ini belum memiliki dua tim.');

                return;
            }

            if (! in_array((int) $this->input('winner_club_id'), $teams, true)) {
                $validator->errors()->add('winner_club_id', 'Pemenang harus salah satu klub yang tampil di pertandingan ini.');
            }
        });
    }
}
