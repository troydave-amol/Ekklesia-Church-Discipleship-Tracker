<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && ($user->isChurchAdmin() || $user->isRestrictedLeader())
            && $user->hasPermission('lessons.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_id'  => ['required', 'exists:programs,id'],
            'week_number' => ['required', 'integer', 'min:1', 'max:52'],
            'title'       => ['required', 'string', 'max:200'],
            'content'     => ['nullable', 'string'],
        ];
    }
}
