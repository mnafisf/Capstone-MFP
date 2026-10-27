<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'competition_type' => ['required', 'in:league,knockout'],
            'min_age' => ['nullable', 'integer', 'min:1', 'max:60'],
            'max_age' => ['nullable', 'integer', 'min:1', 'max:60'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'yellow_card_threshold' => ['required', 'integer', 'min:1', 'max:20'],
            'yellow_card_ban_matches' => ['required', 'integer', 'min:1', 'max:10'],
            'red_card_ban_matches' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }
}
