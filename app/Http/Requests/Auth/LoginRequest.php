<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        if (! Auth::attempt([
            'email' => $this->string('email')->toString(),
            'password' => $this->string('password')->toString(),
            'is_active' => true,
        ])) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales proporcionadas no son válidas.',
            ]);
        }
    }
}
