<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NodeApiService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('app.nodejs_api_url', 'http://localhost:3000/api');
        $this->timeout = config('app.nodejs_api_timeout', 10);
    }

    public function getHealth()
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/health");

            if ($response->successful()) {
                return [
                    'status' => 'healthy',
                    'data' => $response->json(),
                    'response_time' => $response->transferStats?->getTransferTime()
                ];
            }

            return [
                'status' => 'unhealthy',
                'message' => 'API responded with status: ' . $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('NodeAPI Health Check Failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Connexion impossible: ' . $e->getMessage()
            ];
        }
    }

    public function getStats()
    {
        return Cache::remember('node_api_stats', 300, function () {
            try {
                $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/stats");
                return $response->successful() ? $response->json() : null;
            } catch (\Exception $e) {
                Log::error('NodeAPI Stats Failed: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function clearCache()
    {
        Cache::forget('node_api_stats');
    }
}