<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Services\CacheService;
use App\Services\ContactMessageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Validator;

class ContactMessageApiController extends Controller
{
    /**
     * @var ContactMessageService
     */
    protected $contactMessageService;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * ContactMessageApiController constructor.
     *
     * @param ContactMessageService $contactMessageService
     * @param CacheService $cacheService
     */
    public function __construct(ContactMessageService $contactMessageService, CacheService $cacheService)
    {
        $this->contactMessageService = $contactMessageService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the contact messages.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        // Check if user has permission to view messages
        if (!$this->hasAdminAccess($request)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cacheKey = $this->cacheService->generateKey('api:contact-messages:list', [
            'page' => $request->page ?? 1,
            'status' => $request->status,
            'category' => $request->category,
            'search' => $request->search,
        ]);

        return $this->cacheService->remember($cacheKey, function () use ($request) {
            $messages = $this->contactMessageService->getMessagesList(
                $request->status,
                $request->category,
                $request->search,
                20,
                $request->page ?? 1
            );
            
            return ContactMessageResource::collection($messages);
        });
    }

    /**
     * Display the specified contact message.
     *
     * @param Request $request
     * @param ContactMessage $message
     * @return ContactMessageResource|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, ContactMessage $message)
    {
        // Check if user has permission to view messages
        if (!$this->hasAdminAccess($request)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cacheKey = $this->cacheService->modelKey('api:contact-message:detail', $message);

        return $this->cacheService->remember($cacheKey, function () use ($message) {
            $message->load('category');
            return new ContactMessageResource($message);
        });
    }

    /**
     * Store a newly created contact message.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'category_id' => 'nullable|exists:message_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = $this->contactMessageService->createMessage(
            $validator->validated()
        );

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => new ContactMessageResource($message),
        ], 201);
    }

    /**
     * Update the status of the specified contact message.
     *
     * @param Request $request
     * @param ContactMessage $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, ContactMessage $message)
    {
        // Check if user has permission to update messages
        if (!$this->hasAdminAccess($request)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:new,read,responded,archived',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->contactMessageService->updateMessageStatus(
            $message,
            $request->status
        );

        return response()->json([
            'message' => 'Message status updated successfully',
            'data' => new ContactMessageResource($message->fresh()),
        ]);
    }

    /**
     * Check if the user has admin access.
     *
     * @param Request $request
     * @return bool
     */
    private function hasAdminAccess(Request $request)
    {
        $user = $request->user();
        
        // Check if user has necessary roles/permissions
        return $user && $user->roles->contains(function ($role) {
            return in_array($role->name, ['admin', 'super_admin']);
        });
    }
} 