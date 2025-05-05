<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\CacheService;
use Illuminate\Http\Request;

class NewsController extends Controller
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
     * Display a listing of the news.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // If there's a search, skip caching as results are user-specific
        if ($request->has('search') && $request->search) {
            // Build query for latest news with search
            $searchTerm = '%' . $request->search . '%';
            $query = News::published()->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('excerpt', 'like', $searchTerm)
                  ->orWhere('content', 'like', $searchTerm);
            });
            
            // Get latest news with pagination
            $latestNews = $query->latest('published_at')->paginate(9);
            
            // Preserve search parameter in pagination links
            $latestNews->appends($request->only('search'));
            
            return view('pages.news', [
                'featuredNews' => collect(), // No featured news for search results
                'latestNews' => $latestNews,
                'searchQuery' => $request->search,
            ]);
        }
        
        // For non-search requests, use caching
        
        // Get featured news from cache or database
        $featuredNews = $this->cacheService->remember('featured_news', $this->cacheTime, function () {
            return News::published()->featured()->latest('published_at')->take(3)->get();
        });
        
        // Get page number for cache key
        $page = $request->input('page', 1);
        
        // Get latest news with pagination from cache or database
        $cacheKey = $this->cacheService->collectionKey(News::class, ['page' => $page, 'type' => 'latest']);
        $latestNews = $this->cacheService->remember($cacheKey, $this->cacheTime, function () use ($page) {
            return News::published()->latest('published_at')->paginate(9, ['*'], 'page', $page);
        });

        return view('pages.news', [
            'featuredNews' => $featuredNews,
            'latestNews' => $latestNews,
            'searchQuery' => null,
        ]);
    }

    /**
     * Display the specified news article.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Get news article from cache or database
        $cacheKey = "news_article_{$slug}";
        $news = $this->cacheService->remember($cacheKey, $this->cacheTime, function () use ($slug) {
            return News::where('slug', $slug)->published()->firstOrFail();
        });
        
        // Get related news from cache or database
        $relatedCacheKey = "related_news_{$news->id}";
        $relatedNews = $this->cacheService->remember($relatedCacheKey, $this->cacheTime, function () use ($news) {
            return News::published()
                ->where('id', '!=', $news->id)
                ->latest('published_at')
                ->take(3)
                ->get();
        });

        return view('pages.news-detail', [
            'news' => $news,
            'relatedNews' => $relatedNews,
            'slug' => $slug
        ]);
    }
}
