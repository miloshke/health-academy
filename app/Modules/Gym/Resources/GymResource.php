<?php

namespace App\Modules\Gym\Resources;

use App\Library\FormatDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymResource extends JsonResource
{
    use FormatDataTrait;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'status' => $this->status,
            'locations_count' => $this->whenCounted('locations'),
            'users_count' => $this->whenCounted('users'),
            'created_at' => $this->dateAt($this->created_at),
            'updated_at' => $this->dateAt($this->updated_at),
        ];
    }
}
