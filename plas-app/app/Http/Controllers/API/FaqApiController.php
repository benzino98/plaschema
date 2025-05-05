<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Validator;

class FaqApiController extends Controller
{
    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * FaqApiController constructor.
     *
     * @param CacheService $cacheService
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the FAQs.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $cacheKey = $this->cacheService->generateKey('api:faqs:list', [
            'page' => $request->page ?? 1,
            'category' => $request->category,
            'search' => $request->search,
        ]);

        return $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = Faq::query()
                ->with('category')
                ->where('is_published', true)
                ->when($request->category, function ($query, $category) {
                    return $query->whereHas('category', function ($q) use ($category) {
                        $q->where('slug', $category);
                    });
                })
                ->when($request->search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('question', 'like', "%{$search}%")
                            ->orWhere('answer', 'like', "%{$search}%");
                    });
                })
                ->orderBy('order')
                ->orderBy('id');

            $faqs = $query->paginate(20);
            return FaqResource::collection($faqs);
        });
    }

    /**
     * Display the specified FAQ.
     *
     * @param Faq $faq
     * @return FaqResource
     */
    public function show(Faq $faq)
    {
        $cacheKey = $this->cacheService->modelKey('api:faq:detail', $faq);

        return $this->cacheService->remember($cacheKey, function () use ($faq) {
            $faq->load('category');
            return new FaqResource($faq);
        });
    }

    /**
     * Store a newly created FAQ.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_published' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $faq = Faq::create($validator->validated());

        // Clear cache
        $this->cacheService->clearTag('faqs');

        return response()->json([
            'message' => 'FAQ created successfully',
            'data' => new FaqResource($faq),
        ], 201);
    }

    /**
     * Update the specified FAQ.
     *
     * @param Request $request
     * @param Faq $faq
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Faq $faq)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'sometimes|required|string|max:255',
            'answer' => 'sometimes|required|string',
            'is_published' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $faq->update($validator->validated());

        // Clear cache
        $this->cacheService->clearTag('faqs');

        return response()->json([
            'message' => 'FAQ updated successfully',
            'data' => new FaqResource($faq),
        ]);
    }

    /**
     * Remove the specified FAQ.
     *
     * @param Faq $faq
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        // Clear cache
        $this->cacheService->clearTag('faqs');

        return response()->json([
            'message' => 'FAQ deleted successfully'
        ]);
    }
} 