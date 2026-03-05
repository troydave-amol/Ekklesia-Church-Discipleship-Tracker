<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterChurchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'church_name'       => ['required', 'string', 'max:150'],
            'first_name'        => ['required', 'string', 'max:80'],
            'last_name'         => ['required', 'string', 'max:80'],
            'email'             => ['required', 'email', 'unique:users,email'],
            'password'          => ['required', 'confirmed', Password::min(8)],
            'subscription_plan' => ['required', 'in:free,basic,standard,premium'],
        ];
    }
}