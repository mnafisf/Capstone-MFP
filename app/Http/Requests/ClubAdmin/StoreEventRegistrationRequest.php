<?php

namespace App\Http\Requests\ClubAdmin;

use App\Models\Event;
use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreEventRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ids = $this->input('player_ids', []);
        if (! is_array($ids)) {
            $ids = [];
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        $this->merge([
            'player_ids' => $ids,
        ]);
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
            'player_ids' => ['required', 'array', 'min:1'],
            'player_ids.*' => ['integer', 'distinct', 'exists:players,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $clubId = (int) $this->user()->club_id;
            $eventId = (int) $this->input('event_id');
            $playerIds = $this->input('player_ids', []);

            $event = Event::query()->find($eventId);
            if (! $event instanceof Event) {
                return;
            }

            if (! (bool) $event->is_active) {
                $validator->errors()->add('event_id', 'Event sedang nonaktif dan tidak dapat menerima pendaftaran.');

                return;
            }

            $players = Player::query()
                ->where('club_id', $clubId)
                ->where('status', 'approved')
                ->whereIn('id', $playerIds)
                ->get()
                ->keyBy('id');

            if ($players->count() !== count($playerIds)) {
                $validator->errors()->add('player_ids', 'Beberapa pemain tidak valid atau bukan dari club Anda.');

                return;
            }

            $tooYoungOrOld = [];
            foreach ($playerIds as $id) {
                $player = $players->get($id);
                if (! $player || ! $event->isAgeAllowed($player->age)) {
                    $tooYoungOrOld[] = $player?->name ?? (string) $id;
                }
            }

            if ($tooYoungOrOld !== []) {
                $validator->errors()->add(
                    'player_ids',
                    'Usia tidak sesuai kategori event untuk: '.implode(', ', $tooYoungOrOld).'.'
                );
            }
        });
    }
}
