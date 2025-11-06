<?php

namespace App\Modules\Group\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement policy check
        // return $this->user()->can('update', $this->route('group'));
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_id' => ['sometimes', 'required', 'integer', 'exists:gyms,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive,cancelled,completed'],
            'location_ids' => ['nullable', 'array'],
            'location_ids.*' => ['integer', 'exists:locations,id'],
        ];
    }
}
