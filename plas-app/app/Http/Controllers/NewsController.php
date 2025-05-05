<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    /**
     * Cache time in seconds
     */
    protected $cacheTime = 3600; // 1 hour

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
        $featuredNews = Cache::remember('featured_news', $this->cacheTime, function () {
            return News::published()->featured()->latest('published_at')->take(3)->get();
        });
        
        // Get page number for cache key
        $page = $request->input('page', 1);
        
        // Get latest news with pagination from cache or database
        $latestNews = Cache::remember("latest_news_page_{$page}", $this->cacheTime, function () use ($page) {
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
        $news = Cache::remember("news_article_{$slug}", $this->cacheTime, function () use ($slug) {
            return News::where('slug', $slug)->published()->firstOrFail();
        });
        
        // Get related news from cache or database
        $relatedNews = Cache::remember("related_news_{$news->id}", $this->cacheTime, function () use ($news) {
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
