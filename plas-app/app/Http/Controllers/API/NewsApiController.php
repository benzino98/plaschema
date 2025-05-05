<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsCollection;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="News",
 *     description="API Endpoints for news management"
 * )
 */
class NewsApiController extends Controller
{
    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * NewsApiController constructor.
     *
     * @param CacheService $cacheService
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the news.
     *
     * @OA\Get(
     *     path="/api/news",
     *     summary="Get a list of news articles",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category slug",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Filter featured news only",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of news articles",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/News")),
     *             @OA\Property(property="meta", type="object"),
     *             @OA\Property(property="links", type="object")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return NewsCollection
     */
    public function index(Request $request)
    {
        $cacheKey = $this->cacheService->generateKey('api:news:list', [
            'page' => $request->page ?? 1,
            'category' => $request->category,
            'featured' => $request->featured,
            'search' => $request->search,
        ]);

        return $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = News::query()
                ->with('category')
                ->when($request->category, function ($query, $category) {
                    return $query->whereHas('category', function ($q) use ($category) {
                        $q->where('slug', $category);
                    });
                })
                ->when($request->featured, function ($query) {
                    return $query->where('featured', true);
                })
                ->when($request->search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('content', 'like', "%{$search}%");
                    });
                })
                ->latest('published_at')
                ->latest('id');

            $news = $query->paginate(15);
            return new NewsCollection($news);
        });
    }

    /**
     * Display the specified news item.
     *
     * @OA\Get(
     *     path="/api/news/{news}",
     *     summary="Get a specific news article",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="news",
     *         in="path",
     *         description="News ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News article details",
     *         @OA\JsonContent(ref="#/components/schemas/News")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News article not found"
     *     )
     * )
     *
     * @param News $news
     * @return NewsResource
     */
    public function show(News $news)
    {
        $cacheKey = $this->cacheService->modelKey('api:news:detail', $news);

        return $this->cacheService->remember($cacheKey, function () use ($news) {
            $news->load('category');
            return new NewsResource($news);
        });
    }

    /**
     * Store a newly created news item.
     *
     * @OA\Post(
     *     path="/api/news",
     *     summary="Create a new news article",
     *     tags={"News"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="News Title"),
     *             @OA\Property(property="content", type="string", example="News content here..."),
     *             @OA\Property(property="excerpt", type="string", example="Short excerpt of the news"),
     *             @OA\Property(property="featured", type="boolean", example=false),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="published_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="News article created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="News created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/News")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'featured' => 'nullable|boolean',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        }

        $news = News::create(array_merge(
            $validator->validated(),
            ['image' => $imagePath]
        ));

        // Clear cache
        $this->cacheService->clearTag('news');

        return response()->json([
            'message' => 'News created successfully',
            'data' => new NewsResource($news),
        ], 201);
    }

    /**
     * Update the specified news item.
     *
     * @OA\Put(
     *     path="/api/news/{news}",
     *     summary="Update an existing news article",
     *     tags={"News"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="news",
     *         in="path",
     *         description="News ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated News Title"),
     *             @OA\Property(property="content", type="string", example="Updated news content here..."),
     *             @OA\Property(property="excerpt", type="string", example="Updated short excerpt"),
     *             @OA\Property(property="featured", type="boolean", example=true),
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(property="published_at", type="string", format="date-time", example="2023-02-01T00:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News article updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="News updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/News")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News article not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     *
     * @param Request $request
     * @param News $news
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, News $news)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'excerpt' => 'nullable|string',
            'featured' => 'nullable|boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
            $data['image'] = $imagePath;
        }

        $news->update($data);

        // Clear cache
        $this->cacheService->clearTag('news');

        return response()->json([
            'message' => 'News updated successfully',
            'data' => new NewsResource($news),
        ]);
    }

    /**
     * Remove the specified news item.
     *
     * @OA\Delete(
     *     path="/api/news/{news}",
     *     summary="Delete a news article",
     *     tags={"News"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="news",
     *         in="path",
     *         description="News ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News article deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="News deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News article not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     *
     * @param News $news
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(News $news)
    {
        $news->delete();

        // Clear cache
        $this->cacheService->clearTag('news');

        return response()->json([
            'message' => 'News deleted successfully'
        ]);
    }
} 