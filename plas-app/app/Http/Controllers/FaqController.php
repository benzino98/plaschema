<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Services\CacheService;
use Illuminate\Http\Request;

class FaqController extends Controller
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
     * Display a listing of the FAQs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get all categories for filter tabs - always cache this
        $categories = $this->cacheService->remember('faq_categories', $this->cacheTime, function () {
            return Faq::active()
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->pluck('category');
        });
        
        // If filtering by category, don't use cache
        if ($request->has('category') && $request->category) {
            $query = Faq::active()->ordered();
            $query->category($request->category);
            $faqs = $query->get();
        } else {
            // No filter, use cache for all FAQs
            $faqs = $this->cacheService->remember('faqs_all', $this->cacheTime, function () {
                return Faq::active()->ordered()->get();
            });
        }
        
        return view('pages.faq', [
            'faqs' => $faqs,
            'categories' => $categories,
            'currentCategory' => $request->category,
        ]);
    }
}
