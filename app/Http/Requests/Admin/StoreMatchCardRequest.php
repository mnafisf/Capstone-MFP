<?php

namespace App\Http\Requests\Admin;

use App\Models\MatchModel;
use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMatchCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'player_id' => ['required', 'integer', 'exists:players,id'],
            'club_id' => ['required', 'integer', 'exists:clubs,id'],
            'minute_label' => ['required', 'string', 'max:20', 'regex:/^[0-9]{1,3}(\+[0-9]{1,2})?$/'],
            'card_type' => ['required', 'in:yellow,red'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var MatchModel|null $match */
            $match = $this->route('match');
            if (! $match instanceof MatchModel) {
                return;
            }

            $clubId = (int) $this->input('club_id');
            if (! in_array($clubId, [(int) $match->home_club_id, (int) $match->away_club_id], true)) {
                $validator->errors()->add('club_id', 'Club harus tim kandang atau tamu pada pertandingan ini.');
            }

            $playerId = (int) $this->input('player_id');
            $player = Player::query()->find($playerId);
            if ($player && (int) $player->club_id !== $clubId) {
                $validator->errors()->add('player_id', 'Pemain tidak terdaftar pada club yang dipilih.');
            }
        });
    }
}