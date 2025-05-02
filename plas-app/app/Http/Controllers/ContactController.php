<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\MessageCategory;
use App\Services\ContactMessageService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    protected $contactMessageService;

    /**
     * Create a new controller instance.
     *
     * @param ContactMessageService $contactMessageService
     */
    public function __construct(ContactMessageService $contactMessageService)
    {
        $this->contactMessageService = $contactMessageService;
    }

    /**
     * Display the contact page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = MessageCategory::all();
        return view('pages.contact', compact('categories'));
    }

    /**
     * Store a new contact message.
     *
     * @param StoreContactMessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreContactMessageRequest $request)
    {
        $this->contactMessageService->createMessage($request->validated());
        
        return redirect()->route('contact')->with('success', 'Your message has been sent successfully. We will get back to you soon.');
    }
} 