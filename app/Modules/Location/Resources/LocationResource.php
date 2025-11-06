<?php

namespace App\Modules\Location\Resources;

use App\Library\FormatDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    use FormatDataTrait;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gym_id' => $this->gym_id,
            'gym' => $this->whenLoaded('gym', fn() => [
                'id' => $this->gym->id,
                'name' => $this->gym->name,
                'slug' => $this->gym->slug,
            ]),
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'users_count' => $this->whenCounted('users'),
            'groups_count' => $this->whenCounted('groups'),
            'created_at' => $this->dateAt($this->created_at),
            'updated_at' => $this->dateAt($this->updated_at),
        ];
    }
}
