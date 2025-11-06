<?php

namespace App\Modules\Gym\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GymCollection extends ResourceCollection
{
    public $collects = GymResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
