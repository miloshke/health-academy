<?php

namespace App\Modules\Gym\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGymRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement policy check
        // return $this->user()->can('create', Gym::class);
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:gyms'],
            'description' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive,suspended'],
        ];
    }
}
