<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscipleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['pastor', 'leader']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('disciple');

        return [
            'first_name'             => ['required', 'string', 'max:80'],
            'last_name'              => ['required', 'string', 'max:80'],
            'nickname'               => ['nullable', 'string', 'max:60'],
            'email'                  => ['required', 'email', "unique:users,email,{$userId}"],
            'mobile_number'          => ['nullable', 'string', 'max:20'],
            'gender'                 => ['nullable', 'in:male,female'],
            'civil_status'           => ['nullable', 'in:single,married,widowed,separated'],
            'date_of_birth'          => ['nullable', 'date', 'before:today'],
            'address'                => ['nullable', 'string', 'max:255'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'status'                 => ['required', 'in:active,inactive,visitor,new_member'],
            'destiny_training_level' => ['nullable', 'in:life_class,sol1,sol2,sol3,graduate'],
            'leader_id'              => ['nullable', 'exists:users,id'],
            'satellite'              => ['nullable', 'string', 'max:100'],
            'joined_at'              => ['nullable', 'date'],
            'baptized_at'            => ['nullable', 'date'],
        ];
    }
}
