<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Services\CacheService;
use Illuminate\Http\Request;

class PlansController extends Controller
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
     * Display the health plans page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get FAQs related to health plans - using cache
        $plansFaqs = $this->cacheService->remember('faqs_plans_page', $this->cacheTime, function () {
            return Faq::active()
                ->where('category', 'Healthcare Plans')
                ->forPlansPage()
                ->ordered()
                ->limit(3)
                ->get();
        });
        
        return view('pages.plans', [
            'plansFaqs' => $plansFaqs,
        ]);
    }
} 