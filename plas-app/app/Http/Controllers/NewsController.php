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

        // Format the content for proper paragraph display
        $formattedContent = $this->formatNewsContent($news->content);

        return view('pages.news-detail', [
            'news' => $news,
            'relatedNews' => $relatedNews,
            'slug' => $slug,
            'formattedContent' => $formattedContent
        ]);
    }

    /**
     * Format news content with proper paragraph breaks
     *
     * @param string $content
     * @return string
     */
    private function formatNewsContent($content)
    {
        // If content already has HTML tags, return as is
        if (strip_tags($content) !== $content) {
            return $content;
        }

        // Clean up the content first
        $content = trim($content);
        
        // If content is empty, return empty string
        if (empty($content)) {
            return '';
        }
        
        // Handle different paragraph separators
        // Split by double line breaks, or single line breaks if no doubles exist
        if (strpos($content, "\n\n") !== false) {
            $paragraphs = preg_split('/\n\s*\n/', $content);
        } else {
            // If no double line breaks, split by single line breaks
            $paragraphs = preg_split('/\n/', $content);
        }
        
        // Filter out empty paragraphs and wrap each in <p> tags
        $formattedParagraphs = array_filter($paragraphs, function($paragraph) {
            return trim($paragraph) !== '';
        });
        
        // If no valid paragraphs, wrap the entire content in one paragraph
        if (empty($formattedParagraphs)) {
            return '<p class="mb-4 leading-relaxed text-gray-600">' . e($content) . '</p>';
        }
        
        // Wrap each paragraph in <p> tags with consistent styling
        $formattedParagraphs = array_map(function ($paragraph) {
            $paragraph = trim($paragraph);

            return '<p class="mb-4 leading-relaxed text-gray-600">' . e($paragraph) . '</p>';
        }, $formattedParagraphs);
        
        // Join paragraphs with line breaks
        return implode("\n", $formattedParagraphs);
    }
}
