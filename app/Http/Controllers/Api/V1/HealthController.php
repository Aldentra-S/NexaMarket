<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    /**
     * Check the operational health status of the REST API.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'NexaMarket REST API is healthy and operational.',
            'data' => [
                'service' => 'NexaMarket API',
                'version' => 'v1',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }
}
