<?php

namespace App\Modules\Group\Resources;

use App\Library\FormatDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'description' => $this->description,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d H:i:s') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d H:i:s') : null,
            'max_participants' => $this->max_participants,
            'status' => $this->status,
            'locations' => $this->whenLoaded('locations', fn() => $this->locations->map(fn($location) => [
                'id' => $location->id,
                'name' => $location->name,
                'city' => $location->city,
            ])),
            'locations_count' => $this->whenCounted('locations'),
            'users_count' => $this->whenCounted('users'),
            'enrolled_count' => $this->when(method_exists($this->resource, 'enrolledCount'), fn() => $this->enrolledCount()),
            'is_full' => $this->when(method_exists($this->resource, 'isFull'), fn() => $this->isFull()),
            'created_at' => $this->dateAt($this->created_at),
            'updated_at' => $this->dateAt($this->updated_at),
        ];
    }
}
