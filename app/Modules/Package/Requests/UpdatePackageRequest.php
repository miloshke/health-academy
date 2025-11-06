<?php

namespace App\Modules\Package\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Implement policy check
        // return $this->user()->can('update', $this->route('package'));
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_id' => ['sometimes', 'required', 'integer', 'exists:gyms,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'duration_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'benefits' => ['nullable', 'json'],
            'group_access_limit' => ['nullable', 'integer', 'min:0'],
            'unlimited_access' => ['boolean'],
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive'],
        ];
    }
}
