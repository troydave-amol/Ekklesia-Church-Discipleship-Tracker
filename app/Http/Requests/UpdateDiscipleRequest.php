<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDiscipleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isChurchAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'first_name'             => ['required', 'string', 'max:80'],
            'last_name'              => ['required', 'string', 'max:80'],
            'nickname'               => ['nullable', 'string', 'max:60'],
            'email'                  => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password'               => ['nullable', 'string', 'min:8'],
            'mobile_number'          => ['nullable', 'string', 'max:20'],
            'gender'                 => ['nullable', 'in:male,female'],
            'civil_status'           => ['nullable', 'in:single,married,widowed,separated'],
            'date_of_birth'          => ['nullable', 'date', 'before:today'],
            'address'                => ['nullable', 'string', 'max:255'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'state'                  => ['nullable', 'string', 'max:100'],
            'postal_code'            => ['nullable', 'string', 'max:20'],
            'role'                   => ['nullable', 'in:disciple,program_leader,cell_leader'],
            'status'                 => ['required', 'in:active,inactive,visitor,new_member'],
            'destiny_training_level' => ['nullable', 'in:life_class,sol1,sol2,sol3,graduate'],
            'leader_id'              => ['nullable', 'exists:users,id'],
            'satellite'              => ['nullable', 'string', 'max:100'],
            'joined_at'              => ['nullable', 'date'],
            'baptized_at'            => ['nullable', 'date'],
        ];
    }
}
