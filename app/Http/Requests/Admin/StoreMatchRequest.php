<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
            'event_bracket_match_id' => ['nullable', 'exists:event_bracket_matches,id'],
            'home_club_id' => ['required', 'different:away_club_id', 'exists:clubs,id'],
            'away_club_id' => ['required', 'exists:clubs,id'],
            'stadium_id' => ['nullable', 'exists:stadiums,id'],
            'referee_id' => ['nullable', 'exists:referees,id'],
            'referee_2_id' => ['nullable', 'exists:referees,id', 'different:referee_id'],
            'referee_3_id' => ['nullable', 'exists:referees,id', 'different:referee_id', 'different:referee_2_id'],
            'match_date' => ['required', 'date'],
        ];
    }
}
