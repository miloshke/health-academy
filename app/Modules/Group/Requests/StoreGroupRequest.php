<?php

namespace App\Modules\Group\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement policy check
        // return $this->user()->can('create', Group::class);
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_id' => ['required', 'integer', 'exists:gyms,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:active,inactive,cancelled,completed'],
            'location_ids' => ['nullable', 'array'],
            'location_ids.*' => ['integer', 'exists:locations,id'],
        ];
    }
}
