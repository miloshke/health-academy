<?php

namespace App\Modules\Package\Resources;

use App\Library\FormatDataTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'price' => (float) $this->price,
            'duration_days' => $this->duration_days,
            'benefits' => $this->benefits,
            'group_access_limit' => $this->group_access_limit,
            'unlimited_access' => $this->unlimited_access,
            'status' => $this->status,
            'users_count' => $this->whenCounted('users'),
            'active_subscriptions_count' => $this->whenCounted('activeSubscriptions'),
            'created_at' => $this->dateAt($this->created_at),
            'updated_at' => $this->dateAt($this->updated_at),
        ];
    }
}
