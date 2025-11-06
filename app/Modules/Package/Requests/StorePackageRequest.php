<?php

namespace App\Modules\Package\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement policy check
        // return $this->user()->can('create', Package::class);
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_id' => ['required', 'integer', 'exists:gyms,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'benefits' => ['nullable', 'json'],
            'group_access_limit' => ['nullable', 'integer', 'min:0'],
            'unlimited_access' => ['boolean'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }
}
