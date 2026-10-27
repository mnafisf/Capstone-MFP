<?php

namespace App\Http\Requests\ClubAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClubColorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shirt_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'pants_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'socks_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
