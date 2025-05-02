<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Viewing activity logs requires proper permission
        $this->middleware('permission:view-activity-logs');
    }
    
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');
            
        // Filter by action if specified
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by entity type if specified
        if ($request->has('entity_type') && $request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }
        
        // Filter by user if specified
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
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
        $actions = ActivityLog::distinct()->pluck('action');
        $entityTypes = ActivityLog::distinct()->pluck('entity_type');
        $userIds = ActivityLog::distinct()->pluck('user_id');
        
        return view('admin.activity.index', compact(
            'logs', 
            'actions', 
            'entityTypes', 
            'userIds'
        ));
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('admin.activity.show', compact('activityLog'));
    }
} 