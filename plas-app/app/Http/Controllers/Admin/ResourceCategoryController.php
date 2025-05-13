<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResourceCategoryRequest;
use App\Models\ResourceCategory;
use App\Services\ActivityLogService;
use App\Services\ResourceCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResourceCategoryController extends Controller
{
    protected $resourceCategoryService;
    protected $activityLogService;

    public function __construct(
        ResourceCategoryService $resourceCategoryService,
        ActivityLogService $activityLogService
    ) {
        $this->resourceCategoryService = $resourceCategoryService;
        $this->activityLogService = $activityLogService;
        $this->middleware('permission:view-resource-categories')->only(['index', 'show']);
        $this->middleware('permission:create-resource-categories')->only(['create', 'store']);
        $this->middleware('permission:edit-resource-categories')->only(['edit', 'update']);
        $this->middleware('permission:delete-resource-categories')->only(['destroy']);
    }

    /**
     * Display a listing of the resource categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $categories = $this->resourceCategoryService->getAllPaginated(
                $request->get('search'),
                $request->get('per_page', 15),
                $request->get('sort_by', 'created_at'),
                $request->get('sort_direction', 'desc')
            );

            return view('admin.resources.categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Error in ResourceCategoryController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching categories: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource category.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $parentCategories = $this->resourceCategoryService->getAllForSelect();
        return view('admin.resources.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource category in storage.
     *
     * @param  \App\Http\Requests\Admin\ResourceCategoryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ResourceCategoryRequest $request)
    {
        try {
            $category = $this->resourceCategoryService->create($request->validated());
            
            $this->activityLogService->log(
                'created',
                'resource_category',
                $category->id,
                'Resource category "' . $category->name . '" was created'
            );

            return redirect()->route('admin.resource-categories.index')
                ->with('success', 'Resource category created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in ResourceCategoryController@store: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource category.
     *
     * @param  \App\Models\ResourceCategory  $category
     * @return \Illuminate\View\View
     */
    public function show(ResourceCategory $resourceCategory)
    {
        return view('admin.resources.categories.show', [
            'category' => $resourceCategory,
            'resources' => $resourceCategory->resources()->paginate(10)
        ]);
    }

    /**
     * Show the form for editing the specified resource category.
     *
     * @param  \App\Models\ResourceCategory  $category
     * @return \Illuminate\View\View
     */
    public function edit(ResourceCategory $resourceCategory)
    {
        $parentCategories = $this->resourceCategoryService->getAllForSelect($resourceCategory->id);
        return view('admin.resources.categories.edit', [
            'category' => $resourceCategory,
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Update the specified resource category in storage.
     *
     * @param  \App\Http\Requests\Admin\ResourceCategoryRequest  $request
     * @param  \App\Models\ResourceCategory  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ResourceCategoryRequest $request, ResourceCategory $resourceCategory)
    {
        try {
            $this->resourceCategoryService->update($resourceCategory, $request->validated());
            
            $this->activityLogService->log(
                'updated',
                'resource_category',
                $resourceCategory->id,
                'Resource category "' . $resourceCategory->name . '" was updated'
            );

            return redirect()->route('admin.resource-categories.index')
                ->with('success', 'Resource category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in ResourceCategoryController@update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ResourceCategory  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ResourceCategory $resourceCategory)
    {
        try {
            $name = $resourceCategory->name;
            $id = $resourceCategory->id;
            
            $this->resourceCategoryService->delete($resourceCategory);
            
            $this->activityLogService->log(
                'deleted',
                'resource_category',
                $id,
                'Resource category "' . $name . '" was deleted'
            );

            return redirect()->route('admin.resource-categories.index')
                ->with('success', 'Resource category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in ResourceCategoryController@destroy: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the category: ' . $e->getMessage());
        }
    }

    /**
     * Display activity logs for resource categories.
     *
     * @return \Illuminate\View\View
     */
    public function activity()
    {
        $logs = $this->activityLogService->getLogsForEntity('resource_category', 15);
        return view('admin.resources.categories.activity', compact('logs'));
    }

    /**
     * Handle bulk actions on resource categories.
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
                return redirect()->back()->with('error', 'No categories selected.');
            }

            switch ($action) {
                case 'delete':
                    $this->resourceCategoryService->bulkDelete($ids);
                    $message = 'Selected categories deleted successfully.';
                    break;
                default:
                    return redirect()->back()->with('error', 'Invalid action selected.');
            }

            $this->activityLogService->log(
                'bulk-' . $action,
                'resource_category',
                implode(',', $ids),
                'Bulk action "' . $action . '" performed on resource categories'
            );

            return redirect()->route('admin.resource-categories.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error in ResourceCategoryController@bulkAction: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while performing bulk action: ' . $e->getMessage());
        }
    }
} 