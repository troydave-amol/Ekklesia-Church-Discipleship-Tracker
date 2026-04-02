<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $u = auth()->user();

        return $u->isChurchAdmin() || $u->isRestrictedLeader();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lesson_id'         => ['required', 'exists:lessons,id'],
            'date'              => ['required', 'date', 'before_or_equal:today'],
            'records'           => ['required', 'array'],
            'records.*.user_id' => ['required', 'exists:users,id'],
            'records.*.status'  => ['required', 'in:present,absent'],
        ];
    }
}
