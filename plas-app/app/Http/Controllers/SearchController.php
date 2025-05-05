<?php

namespace App\Http\Controllers;

use App\Models\HealthcareProvider;
use App\Models\News;
use App\Models\Faq;
use App\Services\SearchService;
use App\Services\CacheService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Cache time in seconds
     */
    protected $cacheTime = 3600; // 1 hour
    
    /**
     * The search service instance
     */
    protected $searchService;
    
    /**
     * The cache service instance
     */
    protected $cacheService;

    /**
     * Create a new controller instance.
     * 
     * @param SearchService $searchService
     * @param CacheService $cacheService
     * @return void
     */
    public function __construct(SearchService $searchService, CacheService $cacheService)
    {
        $this->searchService = $searchService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display the search page with forms for advanced filtering.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Load necessary data for filter dropdowns
        $categories = $this->cacheService->remember('search.categories', $this->cacheTime, function () {
            return \App\Models\Category::all();
        });
        
        // Locations (extracted from providers)
        $locations = $this->cacheService->remember('search.locations', $this->cacheTime, function () {
            return HealthcareProvider::select('city')->distinct()->whereNotNull('city')->pluck('city');
        });
        
        return view('pages.search', compact('categories', 'locations'));
    }

    /**
     * Process search query and return results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
        $type = $request->input('type', 'all');
        $category = $request->input('category');
        $location = $request->input('location');
        
        // Generate cache key based on search parameters
        $cacheKey = "search.{$type}.{$searchTerm}.{$category}.{$location}." . $request->page ?? 1;
        
        // Cache search results for 10 minutes (600 seconds) only if we have a search term
        // Don't cache if it's just browsing with filters
        $results = null;
        
        if (!empty($searchTerm)) {
            $results = $this->cacheService->remember($cacheKey, 600, function () use ($searchTerm, $type, $category, $location, $request) {
                return $this->searchService->search($searchTerm, $type, $category, $location, $request->all());
            });
        } else {
            $results = $this->searchService->search($searchTerm, $type, $category, $location, $request->all());
        }
        
        // Get the category and location data for filter dropdowns
        $categories = $this->cacheService->remember('search.categories', $this->cacheTime, function () {
            return \App\Models\Category::all();
        });
        
        $locations = $this->cacheService->remember('search.locations', $this->cacheTime, function () {
            return HealthcareProvider::select('city')->distinct()->whereNotNull('city')->pluck('city');
        });
        
        return view('pages.search-results', compact(
            'results', 
            'searchTerm', 
            'type', 
            'category', 
            'location',
            'categories',
            'locations'
        ));
    }
} 