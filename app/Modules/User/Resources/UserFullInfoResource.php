<?php

namespace App\Modules\User\Resources;

use App\Library\FormatDataTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFullInfoResource extends JsonResource
{
    use FormatDataTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'status' => $this->status,
            'birthdate' => $this->birthdate?->format('Y-m-d'),
            'gender' => $this->gender,
            'role' => $this->role ? (User::ROLE_NAMES[$this->role] ?? $this->role) : null,
            'email_verified_at' => $this->dateAt($this->email_verified_at),
            'created_at' => $this->dateAt($this->created_at),
            'updated_at' => $this->dateAt($this->updated_at),
        ];
    }
}
