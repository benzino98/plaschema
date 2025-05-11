<?php

namespace App\Http\Controllers;

use App\Models\News;
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
     * Create a new controller instance.
     * 
     * @param CacheService $cacheService
     * @return void
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
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

        return view('pages.home', [
            'latestNews' => $latestNews,
        ]);
    }
} 