<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Services\ResourceCategoryService;
use App\Services\ResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResourceController extends Controller
{
    protected $resourceService;
    protected $resourceCategoryService;
    
    public function __construct(
        ResourceService $resourceService,
        ResourceCategoryService $resourceCategoryService
    ) {
        $this->resourceService = $resourceService;
        $this->resourceCategoryService = $resourceCategoryService;
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
            $categoryId = $request->get('category');
            $search = $request->get('search');
            $featured = $request->has('featured') ? true : null;
            
            $resources = $this->resourceService->getPublicResourcesPaginated(
                $search,
                $categoryId,
                $featured,
                12,
                'created_at',
                'desc'
            );
            
            $categories = $this->resourceCategoryService->getAllActive();
            $featuredResources = $this->resourceService->getFeaturedResources(3);
            
            return view('pages.resources.index', compact('resources', 'categories', 'featuredResources'));
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@index: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'An error occurred while loading resources.');
        }
    }
    
    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        try {
            $resource = $this->resourceService->getBySlug($slug);
            
            if (!$resource) {
                return redirect()->route('resources.index')->with('error', 'Resource not found.');
            }
            
            $relatedResources = $this->resourceService->getRelatedResources($resource, 3);
            
            return view('pages.resources.show', compact('resource', 'relatedResources'));
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@show: ' . $e->getMessage());
            return redirect()->route('resources.index')->with('error', 'An error occurred while loading the resource.');
        }
    }
    
    /**
     * Download the resource file.
     *
     * @param  string  $slug
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($slug)
    {
        try {
            $resource = $this->resourceService->getBySlug($slug);
            
            if (!$resource) {
                return redirect()->route('resources.index')->with('error', 'Resource not found.');
            }
            
            return $this->resourceService->downloadResource($resource);
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@download: ' . $e->getMessage());
            return redirect()->route('resources.show', $slug)->with('error', 'An error occurred while downloading the resource.');
        }
    }
    
    /**
     * Display resources by category.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function category($slug)
    {
        try {
            $category = $this->resourceCategoryService->getBySlug($slug);
            
            if (!$category) {
                return redirect()->route('resources.index')->with('error', 'Category not found.');
            }
            
            $resources = $this->resourceService->getResourcesByCategory(
                $category->id,
                12,
                'created_at',
                'desc'
            );
            
            $categories = $this->resourceCategoryService->getAllActive();
            
            return view('pages.resources.category', compact('resources', 'category', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in ResourceController@category: ' . $e->getMessage());
            return redirect()->route('resources.index')->with('error', 'An error occurred while loading category resources.');
        }
    }
} 