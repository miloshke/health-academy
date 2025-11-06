<?php

namespace App\Modules\Gym\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGymRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement policy check
        // return $this->user()->can('update', $this->route('gym'));
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('gyms')->ignore($this->route('gym'))],
            'description' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive,suspended'],
        ];
    }
}
