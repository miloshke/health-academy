<?php

namespace App\Modules\Package\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PackageCollection extends ResourceCollection
{
    public $collects = PackageResource::class;

    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
