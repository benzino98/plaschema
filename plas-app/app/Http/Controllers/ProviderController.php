<?php

namespace App\Http\Controllers;

use App\Models\HealthcareProvider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Display a listing of the healthcare providers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
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
        
        // Get all categories for filter dropdown
        $categories = HealthcareProvider::active()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
        
        // Get providers with pagination    
        $providers = $query->orderBy('name')->paginate(10);
        
        // Preserve query parameters in pagination links
        if ($request->has('search') || $request->has('category')) {
            $providers->appends($request->only('search', 'category'));
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
        $provider = HealthcareProvider::active()->findOrFail($id);
        
        return view('pages.provider-detail', [
            'provider' => $provider,
        ]);
    }
}
