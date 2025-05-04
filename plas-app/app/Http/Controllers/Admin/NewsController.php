<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\News;
use App\Services\ImageService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
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
        $this->middleware('permission:view-news')->only(['index', 'show']);
        $this->middleware('permission:create-news')->only(['create', 'store']);
        $this->middleware('permission:edit-news')->only(['edit', 'update']);
        $this->middleware('permission:delete-news')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = News::query()->orderBy('created_at', 'desc');
        
        // Add search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('excerpt', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $news = $query->paginate(10)->withQueryString();
        
        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsRequest $request)
    {
        // Validation handled by the form request
        $validated = $request->validated();
        
        // Handle file upload with optimization
        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->imageService->store(
                $request->file('image'),
                'news',
                1200, // Max width
                800   // Max height
            );
        }

        try {
            $news = News::create($validated);
            
            // Log the activity
            $this->activityLogService->logCreated($news);
            
            return redirect()->route('admin.news.index')
                ->with('success', 'News article created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'There was a problem creating the news article: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsRequest $request, string $id)
    {
        $news = News::findOrFail($id);
        
        // Validation handled by the form request
        $validated = $request->validated();
        
        // Store original values for logging
        $originalValues = $news->getAttributes();

        try {
            // Handle file upload with optimization
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($news->image_path) {
                    $this->imageService->delete($news->image_path);
                }
                
                $validated['image_path'] = $this->imageService->store(
                    $request->file('image'),
                    'news',
                    1200, // Max width
                    800   // Max height
                );
            }

            $news->update($validated);
            
            // Log the activity
            $this->activityLogService->logUpdated($news, $originalValues);
            
            return redirect()->route('admin.news.index')
                ->with('success', 'News article updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'There was a problem updating the news article: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $news = News::findOrFail($id);
        
        try {
            // Delete image if exists
            if ($news->image_path) {
                $this->imageService->delete($news->image_path);
            }
            
            // Log the activity before deletion
            $this->activityLogService->logDeleted($news);
            
            $news->delete();
            
            return redirect()->route('admin.news.index')
                ->with('success', 'News article deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'There was a problem deleting the news article: ' . $e->getMessage());
        }
    }

    /**
     * Display activity logs for news articles.
     */
    public function activity(Request $request)
    {
        // Check permission
        if (!auth()->user()->can('view-activity-logs')) {
            abort(403, 'Unauthorized action.');
        }

        // Get logs using activity log service
        $logs = $this->activityLogService->getLogsByEntityType('News', 20);
        
        // Get unique values for filters
        $actions = \App\Models\ActivityLog::forEntityType('News')
            ->distinct()
            ->pluck('action');
        
        return view('admin.news.activity', compact('logs', 'actions'));
    }
}
