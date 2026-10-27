<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GenerateEventBracketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_count' => ['required', 'integer', 'min:2', 'max:64'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if ($this->route('event')->competition_type !== 'knockout') {
                $validator->errors()->add('team_count', 'Bagan hanya tersedia untuk event dengan tipe knockout.');
            }

            if ($this->route('event')->hasBracket()) {
                $validator->errors()->add('team_count', 'Bagan sudah dibuat. Hapus bagan lama jika ingin membuat ulang.');
            }
        });
    }
}
