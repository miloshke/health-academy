<?php

namespace App\Modules\Health\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'message' => 'Application live.'
        ]);
    }
}
