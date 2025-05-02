<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProviderRequest;
use App\Models\HealthcareProvider;
use App\Services\ImageService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class HealthcareProviderController extends Controller
{
    /**
     * The image service instance.
     */
    protected $imageService;

    /**
     * The activity log service instance.
     */
    protected $activityLogService;

    /**
     * Create a new controller instance.
     */
    public function __construct(ImageService $imageService, ActivityLogService $activityLogService)
    {
        $this->imageService = $imageService;
        $this->activityLogService = $activityLogService;
        
        // Add permission middleware
        $this->middleware('permission:view-providers')->only(['index', 'show']);
        $this->middleware('permission:create-providers')->only(['create', 'store']);
        $this->middleware('permission:edit-providers')->only(['edit', 'update']);
        $this->middleware('permission:delete-providers')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = HealthcareProvider::query()->orderBy('name');
        
        // Add search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('type', 'like', '%' . $searchTerm . '%')
                  ->orWhere('city', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Add type filter
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Get all provider types for filter dropdown
        $types = HealthcareProvider::select('type')
            ->distinct()
            ->whereNotNull('type')
            ->pluck('type');
            
        $providers = $query->paginate(10)->withQueryString();
        
        return view('admin.providers.index', compact('providers', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.providers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProviderRequest $request)
    {
        // Validation handled by the form request
        $validated = $request->validated();
        
        try {
            // Handle file upload with optimization
            if ($request->hasFile('image')) {
                $validated['image_path'] = $this->imageService->store(
                    $request->file('image'),
                    'providers',
                    800, // Max width
                    600   // Max height
                );
            }
            
            $provider = HealthcareProvider::create($validated);
            
            // Log the activity
            $this->activityLogService->logCreated($provider);
            
            return redirect()->route('admin.providers.index')
                ->with('success', 'Healthcare provider created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'There was a problem creating the healthcare provider: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $provider = HealthcareProvider::findOrFail($id);
        return view('admin.providers.show', compact('provider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $provider = HealthcareProvider::findOrFail($id);
        return view('admin.providers.edit', compact('provider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProviderRequest $request, string $id)
    {
        $provider = HealthcareProvider::findOrFail($id);
        
        // Validation handled by the form request
        $validated = $request->validated();
        
        // Store original values for logging
        $originalValues = $provider->getAttributes();
        
        try {
            // Handle file upload with optimization
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($provider->image_path) {
                    $this->imageService->delete($provider->image_path);
                }
                
                $validated['image_path'] = $this->imageService->store(
                    $request->file('image'),
                    'providers',
                    800, // Max width
                    600   // Max height
                );
            }
            
            $provider->update($validated);
            
            // Log the activity
            $this->activityLogService->logUpdated($provider, $originalValues);
            
            return redirect()->route('admin.providers.index')
                ->with('success', 'Healthcare provider updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'There was a problem updating the healthcare provider: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $provider = HealthcareProvider::findOrFail($id);
        
        try {
            // Delete image if exists
            if ($provider->image_path) {
                $this->imageService->delete($provider->image_path);
            }
            
            // Log the activity before deletion
            $this->activityLogService->logDeleted($provider);
            
            $provider->delete();
            
            return redirect()->route('admin.providers.index')
                ->with('success', 'Healthcare provider deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'There was a problem deleting the healthcare provider: ' . $e->getMessage());
        }
    }

    /**
     * Display activity logs for healthcare providers.
     */
    public function activity(Request $request)
    {
        // Check permission
        if (!auth()->user()->can('view-activity-logs')) {
            abort(403, 'Unauthorized action.');
        }

        $query = \App\Models\ActivityLog::with('user')
            ->where('entity_type', \App\Models\HealthcareProvider::class)
            ->orderBy('created_at', 'desc');
            
        // Filter by action if specified
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by provider type (need to check old_values for deleted providers)
        if ($request->has('type') && $request->type) {
            $type = $request->type;
            $query->where(function($q) use ($type) {
                // For non-deleted providers, we can check directly
                $q->whereHas('entity', function($q) use ($type) {
                    $q->where('type', $type);
                });
                
                // For deleted providers, we need to check old_values
                $q->orWhere(function($q) use ($type) {
                    $q->where('action', 'deleted')
                      ->whereJsonContains('old_values->type', $type);
                });
            });
        }
        
        // Filter by date range if specified
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $logs = $query->paginate(20)->withQueryString();
        
        // Get unique values for filters
        $actions = \App\Models\ActivityLog::where('entity_type', \App\Models\HealthcareProvider::class)
            ->distinct()
            ->pluck('action');
            
        // Get all provider types for filter dropdown
        $types = \App\Models\HealthcareProvider::select('type')
            ->distinct()
            ->whereNotNull('type')
            ->pluck('type');
        
        return view('admin.providers.activity', compact('logs', 'actions', 'types'));
    }
}
