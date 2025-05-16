<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\News;
use App\Services\ImageService;
use App\Services\ActivityLogService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     * The cache service instance.
     */
    protected $cacheService;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        ImageService $imageService, 
        ActivityLogService $activityLogService,
        CacheService $cacheService
    )
    {
        $this->imageService = $imageService;
        $this->activityLogService = $activityLogService;
        $this->cacheService = $cacheService;
        
        // Add permission middleware
        $this->middleware('permission:view-news')->only(['index', 'show']);
        $this->middleware('permission:create-news')->only(['create', 'store']);
        $this->middleware('permission:edit-news')->only(['edit', 'update']);
        $this->middleware('permission:delete-news')->only(['destroy', 'bulkAction']);
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
            $imagePaths = $this->imageService->storeResponsive(
                $request->file('image'),
                'news'
            );
            
            $validated['image_path'] = $imagePaths['original'];
            $validated['image_path_small'] = $imagePaths['small'];
            $validated['image_path_medium'] = $imagePaths['medium'];
            $validated['image_path_large'] = $imagePaths['large'];
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
                // Delete old images if they exist
                if ($news->image_path) {
                    $this->imageService->delete($news->image_path);
                }
                if ($news->image_path_small) {
                    $this->imageService->delete($news->image_path_small);
                }
                if ($news->image_path_medium) {
                    $this->imageService->delete($news->image_path_medium);
                }
                if ($news->image_path_large) {
                    $this->imageService->delete($news->image_path_large);
                }
                
                $imagePaths = $this->imageService->storeResponsive(
                    $request->file('image'),
                    'news'
                );
                
                $validated['image_path'] = $imagePaths['original'];
                $validated['image_path_small'] = $imagePaths['small'];
                $validated['image_path_medium'] = $imagePaths['medium'];
                $validated['image_path_large'] = $imagePaths['large'];
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
            if ($news->image_path_small) {
                $this->imageService->delete($news->image_path_small);
            }
            if ($news->image_path_medium) {
                $this->imageService->delete($news->image_path_medium);
            }
            if ($news->image_path_large) {
                $this->imageService->delete($news->image_path_large);
            }
            
            // Log the activity before deletion
            $this->activityLogService->logDeleted($news);
            
            $news->delete();
            
            // Clear news-related caches
            try {
                $this->cacheService->forget('home_latest_news');
                $this->cacheService->clearTag('news');
                $this->cacheService->deleteByPattern('news_*');
            } catch (\Exception $e) {
                // Log cache error but don't fail the request
                Log::warning('Cache clearing error: ' . $e->getMessage());
            }
            
            return redirect()->route('admin.news.index')
                ->with('success', 'News article deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'There was a problem deleting the news article: ' . $e->getMessage());
        }
    }
    
    /**
     * Perform bulk actions on selected news articles.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkAction(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'action' => 'required|string|in:delete,publish,unpublish,feature,unfeature',
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:news,id',
        ]);
        
        // Get the selected news articles
        $newsItems = News::whereIn('id', $validated['ids'])->get();
        
        // If no items were found, return with an error
        if ($newsItems->isEmpty()) {
            return back()->with('error', 'No news articles were selected.');
        }
        
        try {
            // Perform the selected action
            switch ($validated['action']) {
                case 'delete':
                    foreach ($newsItems as $news) {
                        // Delete image if exists
                        if ($news->image_path) {
                            $this->imageService->delete($news->image_path);
                        }
                        if ($news->image_path_small) {
                            $this->imageService->delete($news->image_path_small);
                        }
                        if ($news->image_path_medium) {
                            $this->imageService->delete($news->image_path_medium);
                        }
                        if ($news->image_path_large) {
                            $this->imageService->delete($news->image_path_large);
                        }
                        
                        // Log the activity before deletion
                        $this->activityLogService->logDeleted($news, ['bulk_action' => true]);
                        
                        $news->delete();
                    }
                    $message = count($newsItems) . ' news article(s) deleted successfully.';
                    break;
                    
                case 'publish':
                    foreach ($newsItems as $news) {
                        $originalValues = $news->getAttributes();
                        $news->published_at = now();
                        $news->save();
                        
                        // Log the activity
                        $this->activityLogService->logUpdated($news, $originalValues, ['bulk_action' => true]);
                    }
                    $message = count($newsItems) . ' news article(s) published successfully.';
                    break;
                    
                case 'unpublish':
                    foreach ($newsItems as $news) {
                        $originalValues = $news->getAttributes();
                        $news->published_at = null;
                        $news->save();
                        
                        // Log the activity
                        $this->activityLogService->logUpdated($news, $originalValues, ['bulk_action' => true]);
                    }
                    $message = count($newsItems) . ' news article(s) unpublished successfully.';
                    break;
                    
                case 'feature':
                    foreach ($newsItems as $news) {
                        $originalValues = $news->getAttributes();
                        $news->is_featured = true;
                        $news->save();
                        
                        // Log the activity
                        $this->activityLogService->logUpdated($news, $originalValues, ['bulk_action' => true]);
                    }
                    $message = count($newsItems) . ' news article(s) featured successfully.';
                    break;
                    
                case 'unfeature':
                    foreach ($newsItems as $news) {
                        $originalValues = $news->getAttributes();
                        $news->is_featured = false;
                        $news->save();
                        
                        // Log the activity
                        $this->activityLogService->logUpdated($news, $originalValues, ['bulk_action' => true]);
                    }
                    $message = count($newsItems) . ' news article(s) unfeatured successfully.';
                    break;
                    
                default:
                    return back()->with('error', 'Invalid action selected.');
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'There was a problem performing the bulk action: ' . $e->getMessage());
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
