<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefereeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'license_type' => ['required', 'string', 'in:A,B,C,D'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'ktp' => ['nullable', 'file', 'mimes:pdf', 'max:1024'],
            'license_certificate' => ['nullable', 'file', 'mimes:pdf', 'max:1024'],
        ];
    }
}
