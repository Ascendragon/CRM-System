<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthCheckController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $database = $this->checkDatabase();
        $redis = $this->checkRedis();

        $isHealthy = $database === 'ok' && $redis === 'ok';

        return response()->json([
            'status' => $isHealthy ? 'ok' : 'degraded',
            'services' => [
                'database' => $database,
                'redis' => $redis,
            ],
        ],
            $isHealthy ? 200 : 503);
    }

    private function checkDatabase(): string
    {
        try {
            DB::select('SELECT 1');

            return 'ok';
        } catch (\Throwable $exception) {
            return 'failed';
        }
    }

    private function checkRedis(): string
    {
        try {
            Redis::connection()->ping();

            return 'ok';
        } catch (\Throwable $exception) {
            return 'failed';
        }
    }
}
