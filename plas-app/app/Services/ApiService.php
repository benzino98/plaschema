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
        $this->baseUrl = config('services.external_api.url', 'https://enrollments.plaschema.app/api');
        $this->cacheService = $cacheService;
    }

    /**
     * Get enrollment statistics from the external API.
     *
     * @return array
     */
    public function getEnrollmentStatistics()
    {
        $cacheKey = 'enrollment_statistics';
        $cacheDuration = 300; // 5 minutes
        
        // Try to get from cache first
        $cachedData = $this->cacheService->get($cacheKey);
        
        // If we have cached data, return it immediately
        if ($cachedData) {
            return $cachedData;
        }
        
        // No cache, try to fetch from API
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/data-records');
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Map and format the data as needed - fix to access the nested data
                $statistics = [
                    'total_count' => $data['data']['total_count'] ?? 0,
                    'formal_count' => $data['data']['formal_count'] ?? 0,
                    'total_informal_count' => $data['data']['total_informal_count'] ?? 0,
                    'bhcpf_count' => $data['data']['bhcpf_count'] ?? 0,
                    'equity_count' => $data['data']['equity_count'] ?? 0,
                    'spouse_count' => $data['data']['spouse_count'] ?? 0,
                    'children_count' => $data['data']['children_count'] ?? 0,
                    'principals_count' => $data['data']['principals_count'] ?? 0,
                    'last_updated' => now()->toDateTimeString(),
                ];
                
                // Cache the result
                $this->cacheService->put($cacheKey, $statistics, $cacheDuration);
                
                return $statistics;
            }
            
            Log::warning('External API returned unsuccessful response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch enrollment statistics from external API', [
                'exception' => $e->getMessage()
            ]);
        }
        
        // API failed, use fallback demo data
        $fallbackData = [
            'total_count' => 219415,
            'formal_count' => 129949,
            'total_informal_count' => 5274,
            'bhcpf_count' => 66194,
            'equity_count' => 17998,
            'spouse_count' => 14586,
            'children_count' => 49769,
            'principals_count' => 65594,
            'last_updated' => now()->toDateTimeString(),
            'is_fallback' => true,
        ];
        
        // Cache the fallback data for a shorter period
        $this->cacheService->put($cacheKey, $fallbackData, 120); // 2 minutes
        
        return $fallbackData;
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
                    'total_count' => $data['data']['total_count'] ?? 0,
                    'formal_count' => $data['data']['formal_count'] ?? 0,
                    'total_informal_count' => $data['data']['total_informal_count'] ?? 0,
                    'bhcpf_count' => $data['data']['bhcpf_count'] ?? 0,
                    'equity_count' => $data['data']['equity_count'] ?? 0,
                    'spouse_count' => $data['data']['spouse_count'] ?? 0,
                    'children_count' => $data['data']['children_count'] ?? 0,
                    'principals_count' => $data['data']['principals_count'] ?? 0,
                    'last_updated' => now()->toDateTimeString(),
                ];
                
                // Update the cache with the new data
                $this->cacheService->put($cacheKey, $statistics, 300);
                
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