<?php

namespace App\Modules\Group\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupCollection extends ResourceCollection
{
    public $collects = GroupResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
