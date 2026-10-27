<?php

namespace App\Http\Requests\ClubAdmin;

use App\Models\Official;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfficialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $officialId = $this->route('official')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', Rule::in(array_keys(Official::POSITIONS))],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', Rule::unique('officials', 'email')->ignore($officialId)],
            'shirt_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'pants_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'socks_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'position' => 'posisi',
            'phone' => 'no HP',
            'email' => 'email',
        ];
    }
}
