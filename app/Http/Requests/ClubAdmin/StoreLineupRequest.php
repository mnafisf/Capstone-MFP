<?php

namespace App\Http\Requests\ClubAdmin;

use App\Models\MatchModel;
use App\Models\Player;
use App\Services\PlayerDisciplineService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLineupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $playerStatuses = (array) $this->input('player_statuses', []);
        $playerPositions = (array) $this->input('player_positions', []);
        $playerCaptains = (array) $this->input('player_captains', []);

        $starters = [];
        $substitutes = [];

        foreach ($playerStatuses as $playerId => $status) {
            $playerId = (int) $playerId;
            if ($status === 'starter') {
                $starters[] = $playerId;
            } elseif ($status === 'substitute') {
                $substitutes[] = $playerId;
            }
        }

        $this->merge([
            'starters' => array_values(array_unique(array_filter($starters))),
            'substitutes' => array_values(array_unique(array_filter($substitutes))),
            'player_statuses' => $playerStatuses,
            'player_positions' => $playerPositions,
            'player_captains' => $playerCaptains,
        ]);
    }

    public function rules(): array
    {
        return [
            'match_id' => ['required', 'exists:matches,id'],
            'starters' => ['required', 'array', 'min:1'],
            'starters.*' => ['integer', 'distinct', 'exists:players,id'],
            'substitutes' => ['nullable', 'array'],
            'substitutes.*' => ['integer', 'distinct', 'exists:players,id'],
            'player_statuses' => ['required', 'array'],
            'player_statuses.*' => ['in:starter,substitute,not_playing'],
            'player_positions' => ['nullable', 'array'],
            'player_positions.*' => ['nullable', 'string', 'max:20'],
            'player_captains' => ['nullable', 'array'],
            'player_captains.*' => ['nullable', 'boolean'],
            'shirt_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'pants_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'socks_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $clubId = (int) $this->user()->club_id;
            $matchId = (int) $this->input('match_id');

            $match = MatchModel::query()->find($matchId);
            if (! $match instanceof MatchModel) {
                return;
            }

            if (! in_array($clubId, [(int) $match->home_club_id, (int) $match->away_club_id], true)) {
                $validator->errors()->add('match_id', 'Club Anda tidak berpartisipasi pada pertandingan ini.');

                return;
            }

            if (! $match->canManageLineups()) {
                $validator->errors()->add('deadline', 'Batas waktu input lineup untuk pertandingan ini sudah berakhir atau belum diatur.');

                return;
            }

            if (! $match->match_date->isFuture()) {
                $validator->errors()->add('match_id', 'Line up hanya bisa diubah sebelum pertandingan dimulai.');

                return;
            }

            $starters = collect($this->input('starters', []))->map(fn ($id) => (int) $id);
            $substitutes = collect($this->input('substitutes', []))->map(fn ($id) => (int) $id);

            if ($starters->isEmpty()) {
                $validator->errors()->add('starters', 'Minimal satu pemain harus menjadi pemain utama.');

                return;
            }

            if ($starters->intersect($substitutes)->isNotEmpty()) {
                $validator->errors()->add('substitutes', 'Pemain yang sama tidak boleh ada di starter dan cadangan.');

                return;
            }

            $allIds = $starters->merge($substitutes)->unique()->values();
            $validCount = Player::query()
                ->where('club_id', $clubId)
                ->where('status', 'approved')
                ->whereIn('id', $allIds)
                ->count();

            if ($validCount !== $allIds->count()) {
                $validator->errors()->add('starters', 'Semua pemain harus dari club Anda dan berstatus disetujui.');

                return;
            }

            $disciplineService = app(PlayerDisciplineService::class);

            foreach ($allIds as $playerId) {
                $status = $disciplineService->getStatusForMatch((int) $playerId, $match, $clubId);

                if ($status['is_suspended']) {
                    $playerName = Player::query()->whereKey($playerId)->value('name') ?? 'Pemain';
                    $reason = $status['suspension_reason'] === 'kartu merah'
                        ? 'kartu merah'
                        : 'akumulasi kartu kuning';

                    $validator->errors()->add(
                        'starters',
                        "{$playerName} tidak bisa dimasukkan susunan pemain karena sedang menjalani larangan akibat {$reason}."
                    );

                    return;
                }
            }
        });
    }
}
