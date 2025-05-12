<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\ApiService;
use App\Services\CacheService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Cache time in seconds
     */
    protected $cacheTime = 3600; // 1 hour
    
    /**
     * The cache service instance
     */
    protected $cacheService;
    
    /**
     * The API service instance
     */
    protected $apiService;
    
    /**
     * Create a new controller instance.
     * 
     * @param CacheService $cacheService
     * @param ApiService $apiService
     * @return void
     */
    public function __construct(CacheService $cacheService, ApiService $apiService)
    {
        $this->cacheService = $cacheService;
        $this->apiService = $apiService;
    }

    /**
     * Display the home page with dynamic content.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get latest news from cache or database
        $latestNews = $this->cacheService->remember(
            'home_latest_news', 
            $this->cacheTime, 
            function () {
                return News::published()
                    ->latest('published_at')
                    ->take(3)
                    ->get();
            }
        );

        // Get enrollment statistics from external API
        $statistics = $this->apiService->getEnrollmentStatistics();

        return view('pages.home', [
            'latestNews' => $latestNews,
            'statistics' => $statistics,
        ]);
    }
    
    /**
     * Refresh enrollment statistics from the API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshStatistics()
    {
        $statistics = $this->apiService->refreshEnrollmentStatistics();
        
        if ($statistics) {
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unable to refresh statistics'
        ], 500);
    }
} 