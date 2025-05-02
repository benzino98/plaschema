<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\MessageCategory;
use App\Services\ContactMessageService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    protected $contactMessageService;
    protected $activityLogService;

    /**
     * Create a new controller instance.
     *
     * @param ContactMessageService $contactMessageService
     * @param ActivityLogService $activityLogService
     */
    public function __construct(
        ContactMessageService $contactMessageService,
        ActivityLogService $activityLogService
    ) {
        $this->contactMessageService = $contactMessageService;
        $this->activityLogService = $activityLogService;
        $this->middleware('role:super-admin');
    }

    /**
     * Display a listing of the contact messages.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $categoryId = $request->get('category_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $query = $this->contactMessageService->getFilteredMessages(
            $status,
            $categoryId,
            $dateFrom,
            $dateTo
        );
        
        $messages = $query->paginate(10);
        $categories = MessageCategory::all();
        
        return view('admin.messages.index', compact('messages', 'categories', 'status', 'categoryId', 'dateFrom', 'dateTo'));
    }

    /**
     * Display the specified contact message.
     *
     * @param ContactMessage $message
     * @return \Illuminate\View\View
     */
    public function show(ContactMessage $message)
    {
        // Mark message as read if it's unread
        if (!$message->is_read) {
            $this->contactMessageService->markAsRead($message->id);
        }
        
        $this->activityLogService->log(
            'view',
            auth()->user()->id,
            'ContactMessage',
            $message->id,
            ['message' => 'Viewed contact message from ' . $message->name]
        );
        
        return view('admin.messages.show', compact('message'));
    }

    /**
     * Update the specified contact message status.
     *
     * @param Request $request
     * @param ContactMessage $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, ContactMessage $message)
    {
        $request->validate([
            'status' => 'required|in:new,read,responded,archived',
        ]);
        
        $oldStatus = $message->status;
        $newStatus = $request->status;
        
        $this->contactMessageService->updateStatus($message->id, $newStatus);
        
        $this->activityLogService->log(
            'update',
            auth()->user()->id,
            'ContactMessage',
            $message->id,
            [
                'message' => 'Updated message status from ' . $oldStatus . ' to ' . $newStatus,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]
        );
        
        return redirect()->route('admin.messages.index')->with('success', 'Message status updated successfully.');
    }

    /**
     * Mark message as responded.
     *
     * @param ContactMessage $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markResponded(ContactMessage $message)
    {
        $this->contactMessageService->markAsResponded($message->id);
        
        $this->activityLogService->log(
            'update',
            auth()->user()->id,
            'ContactMessage',
            $message->id,
            ['message' => 'Marked message as responded']
        );
        
        return redirect()->route('admin.messages.index')->with('success', 'Message marked as responded.');
    }

    /**
     * Archive the specified contact message.
     *
     * @param ContactMessage $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(ContactMessage $message)
    {
        $this->contactMessageService->archiveMessage($message->id);
        
        $this->activityLogService->log(
            'update',
            auth()->user()->id,
            'ContactMessage',
            $message->id,
            ['message' => 'Archived contact message']
        );
        
        return redirect()->route('admin.messages.index')->with('success', 'Message archived successfully.');
    }

    /**
     * Display activity logs for contact messages.
     *
     * @return \Illuminate\View\View
     */
    public function activity()
    {
        $logs = $this->activityLogService->getLogsByEntityType('ContactMessage', 20);
        return view('admin.messages.activity', compact('logs'));
    }
} 