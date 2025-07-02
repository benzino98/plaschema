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
     * API request timeout in seconds
     * 
     * @var int
     */
    protected $timeout;
    
    /**
     * API connect timeout in seconds
     * 
     * @var int
     */
    protected $connectTimeout;

    /**
     * Create a new API service instance.
     *
     * @param  \App\Services\CacheService  $cacheService
     * @return void
     */
    public function __construct(CacheService $cacheService = null)
    {
        $this->baseUrl = config('services.external_api.url', 'https://enrollments.plaschema.app/api');
        $this->cacheService = $cacheService ?? app(CacheService::class);
        $this->timeout = config('services.external_api.timeout', 30); 
        $this->connectTimeout = config('services.external_api.connect_timeout', 15);
    }

    /**
     * Get enrollment statistics from the external API.
     *
     * @return array
     */
    public function getEnrollmentStatistics()
    {
        $cacheKey = 'enrollment_statistics';
        $cacheDuration = 1800; // 30 minutes - increased from 5 to reduce API calls
        
        // Try to get from cache first
        $cachedData = $this->cacheService->get($cacheKey);
        
        // If we have cached data, return it immediately
        if ($cachedData) {
            return $cachedData;
        }
        
        // No cache, try to fetch from API
        try {
            // Try with the primary URL first
            $response = $this->makeApiRequest($this->baseUrl . '/data-records');
            
            if ($response && $response->successful()) {
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
            
            // Log the failure but continue with fallback attempts
            Log::warning('Primary API URL returned unsuccessful response', [
                'status' => $response ? $response->status() : 'No response',
                'body' => $response ? $response->body() : 'No response body'
            ]);
            
        } catch (\Exception $e) {
            // Detailed error logging with API URL
            Log::error('Failed to fetch enrollment statistics from primary API URL', [
                'exception' => $e->getMessage(),
                'api_url' => $this->baseUrl . '/data-records',
                'timeout_setting' => $this->timeout,
                'connect_timeout_setting' => $this->connectTimeout
            ]);
        }
        
        // Try with fallback options if primary URL failed
        try {
            // Option 1: Try different protocol
            $alternativeUrl = str_replace('https://', 'http://', $this->baseUrl);
            if ($alternativeUrl !== $this->baseUrl) {
                Log::info('Trying alternative protocol (HTTP) for API call');
                $response = $this->makeApiRequest($alternativeUrl . '/data-records');
                
                if ($response && $response->successful()) {
                    $data = $response->json();
                    
                    // Map and format the data
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
                        'used_alternative_url' => true,
                    ];
                    
                    // Cache the result
                    $this->cacheService->put($cacheKey, $statistics, $cacheDuration);
                    
                    return $statistics;
                }
            }
            
            // Option 2: Try IP address directly if hostname lookup is failing
            // This would need to be configured or added to your .env file
            $alternativeHost = config('services.external_api.alternative_host');
            if ($alternativeHost) {
                Log::info('Trying alternative host for API call', ['host' => $alternativeHost]);
                $alternativeUrl = str_replace(parse_url($this->baseUrl, PHP_URL_HOST), $alternativeHost, $this->baseUrl);
                $response = $this->makeApiRequest($alternativeUrl . '/data-records');
                
                if ($response && $response->successful()) {
                    $data = $response->json();
                    
                    // Map and format the data
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
                        'used_alternative_host' => true,
                    ];
                    
                    // Cache the result
                    $this->cacheService->put($cacheKey, $statistics, $cacheDuration);
                    
                    return $statistics;
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch enrollment statistics from alternative URLs', [
                'exception' => $e->getMessage()
            ]);
        }
        
        // If there's an existing cached value that's expired but still available
        $oldCache = $this->cacheService->getExpired($cacheKey);
        if ($oldCache) {
            Log::info('Using expired cache data for enrollment statistics due to API failure');
            // Add a flag to indicate this is expired data
            $oldCache['is_expired'] = true;
            return $oldCache;
        }
        
        // All API attempts failed, use fallback demo data
        $fallbackData = $this->getFallbackData();
        
        // Cache the fallback data for a shorter period
        $this->cacheService->put($cacheKey, $fallbackData, 900); // 15 minutes
        
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
            // Try with the primary URL first
            $response = $this->makeApiRequest($this->baseUrl . '/data-records');
            
            if ($response && $response->successful()) {
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
                
                // Update the cache with the new data for a longer period
                $this->cacheService->put($cacheKey, $statistics, 1800); // 30 minutes
                
                return $statistics;
            }
            
            // Log the failure but continue with fallback attempts
            Log::warning('Primary API URL returned unsuccessful response during refresh', [
                'status' => $response ? $response->status() : 'No response',
                'body' => $response ? $response->body() : 'No response body'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to refresh enrollment statistics from primary API URL', [
                'exception' => $e->getMessage(),
                'api_url' => $this->baseUrl . '/data-records',
                'timeout_setting' => $this->timeout,
                'connect_timeout_setting' => $this->connectTimeout
            ]);
        }
        
        // Try with fallback options if primary URL failed
        try {
            // Option 1: Try different protocol
            $alternativeUrl = str_replace('https://', 'http://', $this->baseUrl);
            if ($alternativeUrl !== $this->baseUrl) {
                Log::info('Trying alternative protocol (HTTP) for API refresh');
                $response = $this->makeApiRequest($alternativeUrl . '/data-records');
                
                if ($response && $response->successful()) {
                    $data = $response->json();
                    
                    // Map and format the data
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
                        'used_alternative_url' => true,
                    ];
                    
                    // Cache the result
                    $this->cacheService->put($cacheKey, $statistics, 1800);
                    
                    return $statistics;
                }
            }
            
            // Option 2: Try IP address directly if hostname lookup is failing
            $alternativeHost = config('services.external_api.alternative_host');
            if ($alternativeHost) {
                Log::info('Trying alternative host for API refresh', ['host' => $alternativeHost]);
                $alternativeUrl = str_replace(parse_url($this->baseUrl, PHP_URL_HOST), $alternativeHost, $this->baseUrl);
                $response = $this->makeApiRequest($alternativeUrl . '/data-records');
                
                if ($response && $response->successful()) {
                    $data = $response->json();
                    
                    // Map and format the data
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
                        'used_alternative_host' => true,
                    ];
                    
                    // Cache the result
                    $this->cacheService->put($cacheKey, $statistics, 1800);
                    
                    return $statistics;
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to refresh enrollment statistics from alternative URLs', [
                'exception' => $e->getMessage()
            ]);
        }
        
        // Return cached data if available
        $cachedData = $this->cacheService->get($cacheKey);
        if ($cachedData) {
            return $cachedData;
        }
        
        // Return fallback data if no cached data
        return $this->getFallbackData();
    }
    
    /**
     * Get fallback data for when the API is unavailable
     *
     * @return array
     */
    protected function getFallbackData()
    {
        return [
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
    }

    /**
     * Make an API request with consistent settings
     * 
     * @param string $url
     * @return \Illuminate\Http\Client\Response|null
     */
    protected function makeApiRequest($url)
    {
        try {
            $request = Http::timeout($this->timeout)
                ->retry(5, 3000) // Retry 5 times with 3 seconds between attempts
                ->connectTimeout($this->connectTimeout); // Increased connection timeout
            
            // Option to disable SSL verification if needed (only in exceptional cases)
            if (config('services.external_api.verify_ssl', true) === false) {
                $request->withoutVerifying();
            }
            
            // Make the request
            return $request->get($url);
        } catch (\Exception $e) {
            Log::error('API request failed', [
                'url' => $url,
                'exception' => $e->getMessage()
            ]);
            return null;
        }
    }
} 