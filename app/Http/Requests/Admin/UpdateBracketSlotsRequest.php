<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBracketSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slots' => ['present', 'array'],
            'slots.*' => ['array'],
            'slots.*.home' => ['nullable', 'integer', 'exists:clubs,id'],
            'slots.*.away' => ['nullable', 'integer', 'exists:clubs,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $event = $this->route('event');

            if ($event->bracketRounds->isEmpty()) {
                $validator->errors()->add('slots', 'Bagan belum dibuat.');

                return;
            }

            $firstRound = $event->bracketRounds->firstWhere('round_number', 1);

            if (! $firstRound) {
                $validator->errors()->add('slots', 'Bagan belum memiliki babak pertama.');

                return;
            }

            $firstRoundMatchIds = $firstRound->matches->pluck('id')->map(fn (int $id): int => $id)->all();
            $participantIds = $event->participants->pluck('club_id')->map(fn (int $id): int => $id)->all();
            $used = [];

            foreach ($this->input('slots', []) as $matchId => $entry) {
                if (! in_array((int) $matchId, $firstRoundMatchIds, true)) {
                    $validator->errors()->add('slots', 'Ada slot yang bukan bagian dari babak pertama.');

                    return;
                }

                foreach (['home', 'away'] as $side) {
                    $clubId = $entry[$side] ?? null;

                    if ($clubId === null || $clubId === '') {
                        continue;
                    }

                    if (! in_array((int) $clubId, $participantIds, true)) {
                        $validator->errors()->add('slots', 'Semua tim pada slot harus klub peserta event ini.');

                        return;
                    }

                    if (in_array((int) $clubId, $used, true)) {
                        $validator->errors()->add('slots', 'Sebuah klub hanya boleh berada di satu slot. Perbaiki pilihan tim yang duplikat.');

                        return;
                    }

                    $used[] = (int) $clubId;
                }
            }
        });
    }

    /**
     * @return array<int, array{home: int|null, away: int|null}>
     */
    public function slotPayload(): array
    {
        $payload = [];

        foreach ($this->input('slots', []) as $matchId => $entry) {
            $payload[(int) $matchId] = [
                'home' => isset($entry['home']) && $entry['home'] !== '' ? (int) $entry['home'] : null,
                'away' => isset($entry['away']) && $entry['away'] !== '' ? (int) $entry['away'] : null,
            ];
        }

        return $payload;
    }
}
