<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get featured news (not affected by search)
        $featuredNews = News::published()->featured()->latest('published_at')->take(3)->get();
        
        // Build query for latest news
        $query = News::published();
        
        // Apply search if provided
        if ($request->has('search') && $request->search) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('excerpt', 'like', $searchTerm)
                  ->orWhere('content', 'like', $searchTerm);
            });
        }
        
        // Get latest news with pagination
        $latestNews = $query->latest('published_at')->paginate(9);
        
        // Preserve search parameter in pagination links
        if ($request->has('search')) {
            $latestNews->appends($request->only('search'));
        }

        return view('pages.news', [
            'featuredNews' => $featuredNews,
            'latestNews' => $latestNews,
            'searchQuery' => $request->search,
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
        $news = News::where('slug', $slug)->published()->firstOrFail();
        $relatedNews = News::published()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.news-detail', [
            'news' => $news,
            'relatedNews' => $relatedNews,
        ]);
    }
}
