<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * The activity log service instance.
     */
    protected $activityLogService;

    /**
     * Create a new controller instance.
     */
    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
        
        // Add permission middleware
        $this->middleware('permission:view-faqs')->only(['index', 'show']);
        $this->middleware('permission:create-faqs')->only(['create', 'store']);
        $this->middleware('permission:edit-faqs')->only(['edit', 'update']);
        $this->middleware('permission:delete-faqs')->only(['destroy', 'bulkAction']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faq::query()->orderBy('order');
        
        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        // Add search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('question', 'like', '%' . $searchTerm . '%')
                  ->orWhere('answer', 'like', '%' . $searchTerm . '%')
                  ->orWhere('category', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Get all categories for filter dropdown
        $categories = Faq::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
            
        $faqs = $query->paginate(15)->withQueryString();
        
        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.faqs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaqRequest $request)
    {
        // Validation handled by the form request
        $validated = $request->validated();

        try {
            $faq = Faq::create($validated);
            
            // Log the activity
            $this->activityLogService->logCreated($faq);

            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'There was a problem creating the FAQ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faqs.show', compact('faq'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faqs.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaqRequest $request, string $id)
    {
        $faq = Faq::findOrFail($id);

        // Validation handled by the form request
        $validated = $request->validated();
        
        // Store original values for logging
        $originalValues = $faq->getAttributes();

        try {
            $faq->update($validated);
            
            // Log the activity
            $this->activityLogService->logUpdated($faq, $originalValues);

            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'There was a problem updating the FAQ: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $faq = Faq::findOrFail($id);
        
        try {
            // Log the activity before deletion
            $this->activityLogService->logDeleted($faq);
            
            $faq->delete();

            return redirect()->route('admin.faqs.index')
                ->with('success', 'FAQ deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'There was a problem deleting the FAQ: ' . $e->getMessage());
        }
    }
    
    /**
     * Perform bulk actions on selected FAQs.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkAction(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'action' => 'required|string|in:delete,change-category',
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:faqs,id',
            'category' => 'nullable|required_if:action,change-category|string|max:255',
        ]);
        
        // Get the selected FAQs
        $faqs = Faq::whereIn('id', $validated['ids'])->get();
        
        // If no items were found, return with an error
        if ($faqs->isEmpty()) {
            return back()->with('error', 'No FAQs were selected.');
        }
        
        try {
            // Perform the selected action
            switch ($validated['action']) {
                case 'delete':
                    foreach ($faqs as $faq) {
                        // Log the activity before deletion
                        $this->activityLogService->logDeleted($faq, ['bulk_action' => true]);
                        
                        $faq->delete();
                    }
                    $message = count($faqs) . ' FAQ(s) deleted successfully.';
                    break;
                    
                case 'change-category':
                    if (!isset($validated['category']) || empty($validated['category'])) {
                        return back()->with('error', 'Please select a category to assign.');
                    }
                    
                    foreach ($faqs as $faq) {
                        $originalValues = $faq->getAttributes();
                        $faq->category = $validated['category'];
                        $faq->save();
                        
                        // Log the activity
                        $this->activityLogService->logUpdated($faq, $originalValues, ['bulk_action' => true]);
                    }
                    $message = count($faqs) . ' FAQ(s) moved to category "' . $validated['category'] . '" successfully.';
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
     * Display activity logs for FAQs.
     */
    public function activity(Request $request)
    {
        // Check permission
        if (!auth()->user()->can('view-activity-logs')) {
            abort(403, 'Unauthorized action.');
        }

        // Get logs using activity log service
        $logs = $this->activityLogService->getLogsByEntityType('Faq', 20);
        
        // Get unique values for filters
        $actions = \App\Models\ActivityLog::forEntityType('Faq')
            ->distinct()
            ->pluck('action');
        
        return view('admin.faqs.activity', compact('logs', 'actions'));
    }
}
