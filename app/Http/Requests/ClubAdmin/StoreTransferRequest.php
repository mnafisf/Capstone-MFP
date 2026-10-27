<?php

namespace App\Http\Requests\ClubAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'player_id' => ['required', 'exists:players,id'],
            'to_club_id' => ['required', 'exists:clubs,id'],
            'document' => ['required', 'file', 'mimes:pdf', 'max:1024'],
        ];
    }
}
