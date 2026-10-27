<?php

namespace App\Http\Requests\Admin;

use App\Models\MatchModel;
use App\Models\Lineup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMatchSubstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'club_id' => ['required', 'integer', 'exists:clubs,id'],
            'minute_label' => ['required', 'string', 'max:20', 'regex:/^[0-9]{1,3}(\+[0-9]{1,2})?$/'],
            'player_out_id' => ['required', 'integer', 'exists:players,id'],
            'player_in_id' => ['required', 'integer', 'exists:players,id', 'different:player_out_id'],
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

            $lineup = Lineup::query()
                ->with('lineupPlayers')
                ->where('match_id', $match->id)
                ->where('club_id', $clubId)
                ->first();

            if (! $lineup) {
                $validator->errors()->add('club_id', 'Susunan pemain untuk klub ini belum tersedia.');
                return;
            }

            $starterIds = $lineup->lineupPlayers
                ->where('position_type', 'starter')
                ->pluck('player_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $substituteIds = $lineup->lineupPlayers
                ->where('position_type', 'substitute')
                ->pluck('player_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $usedOutIds = $match->substitutions()->where('club_id', $clubId)->pluck('player_out_id')->map(fn ($id) => (int) $id)->all();
            $usedInIds = $match->substitutions()->where('club_id', $clubId)->pluck('player_in_id')->map(fn ($id) => (int) $id)->all();

            $activeIds = array_values(array_diff(array_unique(array_merge($starterIds, $usedInIds)), $usedOutIds));

            $playerOutId = (int) $this->input('player_out_id');
            $playerInId = (int) $this->input('player_in_id');

            if (! in_array($playerOutId, $activeIds, true)) {
                $validator->errors()->add('player_out_id', 'Pemain keluar harus diambil dari lineup aktif pertandingan.');
            }

            if (! in_array($playerInId, $substituteIds, true)) {
                $validator->errors()->add('player_in_id', 'Pemain masuk harus diambil dari cadangan lineup klub.');
            }

            $outAlreadyUsed = $match->substitutions()->where('club_id', $clubId)->where('player_out_id', $playerOutId)->exists();
            $inAlreadyUsed = $match->substitutions()->where('club_id', $clubId)->where('player_in_id', $playerInId)->exists();

            if ($outAlreadyUsed) {
                $validator->errors()->add('player_out_id', 'Pemain ini sudah pernah diganti.');
            }

            if ($inAlreadyUsed) {
                $validator->errors()->add('player_in_id', 'Pemain cadangan ini sudah pernah masuk.');
            }
        });
    }
}