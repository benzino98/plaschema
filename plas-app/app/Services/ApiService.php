<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    /**
     * Base URL for the external API
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Cache service for storing API responses
     *
     * @var \App\Services\CacheService
     */
    protected $cacheService;

    /**
     * Create a new API service instance.
     *
     * @param  \App\Services\CacheService  $cacheService
     * @return void
     */
    public function __construct(CacheService $cacheService)
    {
        $this->baseUrl = config('services.external_api.url', 'https://plaschema.app/api');
        $this->cacheService = $cacheService;
    }

    /**
     * Get enrollment statistics from the external API.
     *
     * @return array|null
     */
    public function getEnrollmentStatistics()
    {
        $cacheKey = 'enrollment_statistics';
        $cacheDuration = 3600; // 1 hour
        
        // Try to get from cache first
        return $this->cacheService->remember($cacheKey, $cacheDuration, function () {
            try {
                $response = Http::timeout(5)->get($this->baseUrl . '/data-records');
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Map and format the data as needed
                    return [
                        'total_count' => $data['total_count'] ?? 0,
                        'formal_count' => $data['formal_count'] ?? 0,
                        'total_informal_count' => $data['total_informal_count'] ?? 0,
                        'bhcpf_count' => $data['bhcpf_count'] ?? 0,
                        'equity_count' => $data['equity_count'] ?? 0,
                        'last_updated' => now()->toDateTimeString(),
                    ];
                }
                
                Log::warning('External API returned unsuccessful response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return null;
            } catch (\Exception $e) {
                Log::error('Failed to fetch enrollment statistics from external API', [
                    'exception' => $e->getMessage()
                ]);
                
                return null;
            }
        });
    }

    /**
     * Refresh enrollment statistics by force-fetching from the API.
     *
     * @return array|null
     */
    public function refreshEnrollmentStatistics()
    {
        $cacheKey = 'enrollment_statistics';
        
        // Force a fresh fetch from the API
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/data-records');
            
            if ($response->successful()) {
                $data = $response->json();
                
                $statistics = [
                    'total_count' => $data['total_count'] ?? 0,
                    'formal_count' => $data['formal_count'] ?? 0,
                    'total_informal_count' => $data['total_informal_count'] ?? 0,
                    'bhcpf_count' => $data['bhcpf_count'] ?? 0,
                    'equity_count' => $data['equity_count'] ?? 0,
                    'last_updated' => now()->toDateTimeString(),
                ];
                
                // Update the cache with the new data
                $this->cacheService->put($cacheKey, $statistics, 3600);
                
                return $statistics;
            }
            
            Log::warning('External API returned unsuccessful response during refresh', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to refresh enrollment statistics from external API', [
                'exception' => $e->getMessage()
            ]);
            
            return null;
        }
    }
} 