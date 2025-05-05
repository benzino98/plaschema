<?php

namespace App\Http\Controllers;

use App\Models\HealthcareProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProviderController extends Controller
{
    /**
     * Cache time in seconds
     */
    protected $cacheTime = 3600; // 1 hour

    /**
     * Display a listing of the healthcare providers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get all categories for filter dropdown from cache
        $categories = Cache::remember('provider_categories', $this->cacheTime, function () {
            return HealthcareProvider::active()
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->pluck('category');
        });
        
        // If there's a search or category filter, skip result caching as it's user-specific
        if ($request->has('search') || $request->has('category')) {
            $query = HealthcareProvider::active();
            
            // Search if provided
            if ($request->has('search') && $request->search) {
                $searchTerm = '%' . $request->search . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm)
                      ->orWhere('address', 'like', $searchTerm)
                      ->orWhere('city', 'like', $searchTerm)
                      ->orWhere('provider_type', 'like', $searchTerm)
                      ->orWhere('category', 'like', $searchTerm);
                });
            }
            
            // Filter by category if provided
            if ($request->has('category') && $request->category) {
                $query->category($request->category);
            }
            
            // Get providers with pagination    
            $providers = $query->orderBy('name')->paginate(10);
            
            // Preserve query parameters in pagination links
            $providers->appends($request->only('search', 'category'));
        } else {
            // For non-filtered requests, use caching
            $page = $request->input('page', 1);
            
            // Get providers with pagination from cache or database
            $providers = Cache::remember("providers_page_{$page}", $this->cacheTime, function () use ($page) {
                return HealthcareProvider::active()->orderBy('name')->paginate(10, ['*'], 'page', $page);
            });
        }
        
        return view('pages.providers', [
            'providers' => $providers,
            'categories' => $categories,
            'currentCategory' => $request->category,
            'searchQuery' => $request->search,
        ]);
    }

    /**
     * Display the specified provider.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Get provider from cache or database
        $provider = Cache::remember("provider_{$id}", $this->cacheTime, function () use ($id) {
            return HealthcareProvider::active()->findOrFail($id);
        });
        
        return view('pages.provider-detail', [
            'provider' => $provider,
        ]);
    }
}
