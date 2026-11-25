<?php

namespace App\Http\Requests\Admin;

use App\Services\BracketService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreEventParticipantsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'club_ids' => ['present', 'array'],
            'club_ids.*' => ['integer', 'distinct', 'exists:clubs,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $event = $this->route('event');

            if (! $event->isKnockout()) {
                return;
            }

            $capacity = app(BracketService::class)->bracketCapacity($event);

            if ($capacity !== null && count($this->input('club_ids', [])) > $capacity) {
                $validator->errors()->add(
                    'club_ids',
                    'Jumlah klub melebihi kapasitas bagan ('.$capacity.' slot). Hapus bagan atau buat ulang dengan jumlah tim yang lebih besar.',
                );
            }

            // Ensure all selected clubs are active
            $clubIds = $this->input('club_ids', []);
            if (! empty($clubIds)) {
                $inactiveCount = \App\Models\Club::query()
                    ->whereIn('id', $clubIds)
                    ->where('is_active', false)
                    ->count();

                if ($inactiveCount > 0) {
                    $validator->errors()->add(
                        'club_ids',
                        'Beberapa klub yang dipilih tidak aktif. Hanya klub aktif yang dapat menjadi peserta event.'
                    );
                }
            }
        });
    }
}
