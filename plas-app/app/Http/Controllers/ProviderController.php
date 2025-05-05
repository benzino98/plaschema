<?php

namespace App\Http\Controllers;

use App\Models\HealthcareProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProviderController extends Controller
{
    /**
     * Cache time in seconds
     */
    protected $cacheTime = 3600; // 1 hour

    /**
     * Check if a column exists in the healthcare_providers table
     * 
     * @param string $column
     * @return bool
     */
    protected function columnExists($column)
    {
        return Schema::hasColumn('healthcare_providers', $column);
    }

    /**
     * Display a listing of the healthcare providers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Check if provider_type column exists
        $hasProviderTypeColumn = $this->columnExists('provider_type');
        
        // Get all categories for filter dropdown from cache
        $categories = Cache::remember('provider_categories', $this->cacheTime, function () {
            return HealthcareProvider::active()
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->pluck('category');
        });
        
        // Get all cities for location filter dropdown from cache
        $cities = Cache::remember('provider_cities', $this->cacheTime, function () {
            return HealthcareProvider::active()
                ->select('city')
                ->distinct()
                ->whereNotNull('city')
                ->pluck('city');
        });
        
        // Get provider types only if the column exists
        $providerTypes = collect();
        if ($hasProviderTypeColumn) {
            $providerTypes = Cache::remember('provider_types', $this->cacheTime, function () {
                return HealthcareProvider::active()
                    ->select('provider_type')
                    ->distinct()
                    ->whereNotNull('provider_type')
                    ->pluck('provider_type');
            });
        }
        
        // Initialize query builder
        $query = HealthcareProvider::active();
        
        // Determine if we need to skip caching
        $skipCache = $request->has('search') || $request->has('category') || $request->has('city');
        
        // Add provider_type to skip cache conditions if it exists and is provided
        if ($hasProviderTypeColumn && $request->has('provider_type')) {
            $skipCache = true;
        }
        
        if ($skipCache) {
            // Search if provided
            if ($request->has('search') && $request->search) {
                $searchTerm = '%' . $request->search . '%';
                $query->where(function($q) use ($searchTerm, $hasProviderTypeColumn) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm)
                      ->orWhere('address', 'like', $searchTerm)
                      ->orWhere('city', 'like', $searchTerm)
                      ->orWhere('category', 'like', $searchTerm);
                    
                    // Add provider_type to search columns if it exists
                    if ($hasProviderTypeColumn) {
                        $q->orWhere('provider_type', 'like', $searchTerm);
                    }
                });
            }
            
            // Filter by category if provided
            if ($request->has('category') && $request->category) {
                $query->where('category', $request->category);
            }
            
            // Filter by city if provided
            if ($request->has('city') && $request->city) {
                $query->where('city', $request->city);
            }
            
            // Filter by provider_type if it exists and is provided
            if ($hasProviderTypeColumn && $request->has('provider_type') && $request->provider_type) {
                $query->where('provider_type', $request->provider_type);
            }
            
            // Get providers with pagination    
            $providers = $query->orderBy('name')->paginate(10);
            
            // Preserve query parameters in pagination links
            $queryParams = ['search', 'category', 'city'];
            if ($hasProviderTypeColumn) {
                $queryParams[] = 'provider_type';
            }
            $providers->appends($request->only($queryParams));
        } else {
            // For non-filtered requests, use caching
            $page = $request->input('page', 1);
            
            // Get providers with pagination from cache or database
            $providers = Cache::remember("providers_page_{$page}", $this->cacheTime, function () use ($page) {
                return HealthcareProvider::active()->orderBy('name')->paginate(10, ['*'], 'page', $page);
            });
        }
        
        $data = [
            'providers' => $providers,
            'categories' => $categories,
            'cities' => $cities,
            'currentCategory' => $request->category,
            'currentCity' => $request->city,
            'searchQuery' => $request->search,
            'hasProviderTypeColumn' => $hasProviderTypeColumn,
        ];
        
        // Add provider type data if the column exists
        if ($hasProviderTypeColumn) {
            $data['providerTypes'] = $providerTypes;
            $data['currentProviderType'] = $request->provider_type;
        }
        
        return view('pages.providers', $data);
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
        
        // Get similar providers by category from cache or database
        $similarProviders = Cache::remember("similar_providers_{$id}", $this->cacheTime, function () use ($provider) {
            return HealthcareProvider::active()
                ->where('id', '!=', $provider->id)
                ->where(function($query) use ($provider) {
                    $query->where('category', $provider->category)
                          ->orWhere('city', $provider->city);
                })
                ->take(3)
                ->get();
        });
        
        return view('pages.provider-detail', [
            'provider' => $provider,
            'similarProviders' => $similarProviders,
        ]);
    }
}
