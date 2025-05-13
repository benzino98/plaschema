<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResourceRequest;
use App\Models\Resource;
use App\Services\ActivityLogService;
use App\Services\ResourceCategoryService;
use App\Services\ResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResourceController extends Controller
{
    protected $resourceService;
    protected $resourceCategoryService;
    protected $activityLogService;

    public function __construct(
        ResourceService $resourceService,
        ResourceCategoryService $resourceCategoryService,
        ActivityLogService $activityLogService
    ) {
        $this->resourceService = $resourceService;
        $this->resourceCategoryService = $resourceCategoryService;
        $this->activityLogService = $activityLogService;
        
        $this->middleware('permission:view-resources')->only(['index', 'show']);
        $this->middleware('permission:create-resources')->only(['create', 'store']);
        $this->middleware('permission:edit-resources')->only(['edit', 'update']);
        $this->middleware('permission:delete-resources')->only(['destroy']);
    }

    /**
     * Display a listing of the resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $resources = $this->resourceService->getAllPaginated(
                $request->get('search'),
                $request->get('category_id'),
                $request->get('featured'),
                $request->get('per_page', 15),
                $request->get('sort_by', 'created_at'),
                $request->get('sort_direction', 'desc')
            );

            $categories = $this->resourceCategoryService->getAllForSelect();
            
            return view('admin.resources.index', compact('resources', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching resources: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = $this->resourceCategoryService->getAllForSelect();
        return view('admin.resources.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\ResourceRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ResourceRequest $request)
    {
        try {
            $resource = $this->resourceService->create($request->validated());
            
            $this->activityLogService->log(
                'created',
                'resource',
                $resource->id,
                'Resource "' . $resource->title . '" was created'
            );

            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@store: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the resource: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\View\View
     */
    public function show(Resource $resource)
    {
        $downloadCount = $this->resourceService->getDownloadCount($resource);
        return view('admin.resources.show', compact('resource', 'downloadCount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\View\View
     */
    public function edit(Resource $resource)
    {
        $categories = $this->resourceCategoryService->getAllForSelect();
        return view('admin.resources.edit', compact('resource', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\ResourceRequest  $request
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ResourceRequest $request, Resource $resource)
    {
        try {
            $this->resourceService->update($resource, $request->validated());
            
            $this->activityLogService->log(
                'updated',
                'resource',
                $resource->id,
                'Resource "' . $resource->title . '" was updated'
            );

            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the resource: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Resource $resource)
    {
        try {
            $title = $resource->title;
            $id = $resource->id;
            
            $this->resourceService->delete($resource);
            
            $this->activityLogService->log(
                'deleted',
                'resource',
                $id,
                'Resource "' . $title . '" was deleted'
            );

            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@destroy: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the resource: ' . $e->getMessage());
        }
    }

    /**
     * Display activity logs for resources.
     *
     * @return \Illuminate\View\View
     */
    public function activity()
    {
        $logs = $this->activityLogService->getLogsForEntity('resource', 15);
        return view('admin.resources.activity', compact('logs'));
    }

    /**
     * Handle bulk actions on resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        try {
            $action = $request->input('action');
            $ids = $request->input('ids');
            
            if (empty($ids)) {
                return redirect()->back()->with('error', 'No resources selected.');
            }

            switch ($action) {
                case 'delete':
                    $this->resourceService->bulkDelete($ids);
                    $message = 'Selected resources deleted successfully.';
                    break;
                case 'feature':
                    $this->resourceService->bulkFeature($ids, true);
                    $message = 'Selected resources marked as featured.';
                    break;
                case 'unfeature':
                    $this->resourceService->bulkFeature($ids, false);
                    $message = 'Selected resources removed from featured.';
                    break;
                default:
                    return redirect()->back()->with('error', 'Invalid action selected.');
            }

            $this->activityLogService->log(
                'bulk-' . $action,
                'resource',
                implode(',', $ids),
                'Bulk action "' . $action . '" performed on resources'
            );

            return redirect()->route('admin.resources.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@bulkAction: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while performing bulk action: ' . $e->getMessage());
        }
    }

    /**
     * Display download statistics for resources.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function downloadStats(Request $request)
    {
        try {
            $period = $request->get('period', 'monthly');
            $resourceId = $request->get('resource_id');
            $categoryId = $request->get('category_id');
            
            $stats = $this->resourceService->getDownloadStats($period, $resourceId, $categoryId);
            $topResources = $this->resourceService->getTopDownloaded(10);
            $resources = $this->resourceService->getAllForSelect();
            $categories = $this->resourceCategoryService->getAllForSelect();
            
            return view('admin.resources.stats', compact(
                'stats', 
                'topResources', 
                'resources', 
                'categories', 
                'period', 
                'resourceId', 
                'categoryId'
            ));
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@downloadStats: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching download statistics: ' . $e->getMessage());
        }
    }
} 