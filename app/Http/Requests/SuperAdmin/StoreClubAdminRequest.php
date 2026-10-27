<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClubAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $clubId = $this->route('user')?->club_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$userId ? 'nullable' : 'required', 'string', 'min:8'],
            'club_name' => ['required', 'string', 'max:255'],
            'club_code' => ['required', 'string', 'max:50', Rule::unique('clubs', 'code')->ignore($clubId)],
            'club_logo' => ['nullable', 'image', 'max:2048'],
            'club_is_active' => ['nullable', 'boolean'],
        ];
    }
}
