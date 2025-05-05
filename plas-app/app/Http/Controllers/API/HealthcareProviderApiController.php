<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\HealthcareProviderResource;
use App\Models\HealthcareProvider;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Validator;

class HealthcareProviderApiController extends Controller
{
    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * HealthcareProviderApiController constructor.
     *
     * @param CacheService $cacheService
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the healthcare providers.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $cacheKey = $this->cacheService->generateKey('api:providers:list', [
            'page' => $request->page ?? 1,
            'category' => $request->category,
            'city' => $request->city,
            'is_featured' => $request->is_featured,
            'search' => $request->search,
        ]);

        return $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = HealthcareProvider::query()
                ->with('category')
                ->where('is_active', true)
                ->when($request->category, function ($query, $category) {
                    return $query->whereHas('category', function ($q) use ($category) {
                        $q->where('slug', $category);
                    });
                })
                ->when($request->city, function ($query, $city) {
                    return $query->where('city', $city);
                })
                ->when($request->is_featured, function ($query) {
                    return $query->where('is_featured', true);
                })
                ->when($request->search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('specialties', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name');

            $providers = $query->paginate(12);
            return HealthcareProviderResource::collection($providers);
        });
    }

    /**
     * Display the specified healthcare provider.
     *
     * @param HealthcareProvider $provider
     * @return HealthcareProviderResource
     */
    public function show(HealthcareProvider $provider)
    {
        $cacheKey = $this->cacheService->modelKey('api:provider:detail', $provider);

        return $this->cacheService->remember($cacheKey, function () use ($provider) {
            $provider->load('category');
            return new HealthcareProviderResource($provider);
        });
    }

    /**
     * Store a newly created healthcare provider.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'contact_info' => 'required|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'specialties' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('providers', 'public');
        }

        $provider = HealthcareProvider::create(array_merge(
            $validator->validated(),
            ['image' => $imagePath]
        ));

        // Clear cache
        $this->cacheService->clearTag('providers');

        return response()->json([
            'message' => 'Healthcare provider created successfully',
            'data' => new HealthcareProviderResource($provider),
        ], 201);
    }

    /**
     * Update the specified healthcare provider.
     *
     * @param Request $request
     * @param HealthcareProvider $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, HealthcareProvider $provider)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:100',
            'contact_info' => 'sometimes|required|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'specialties' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('providers', 'public');
            $data['image'] = $imagePath;
        }

        $provider->update($data);

        // Clear cache
        $this->cacheService->clearTag('providers');

        return response()->json([
            'message' => 'Healthcare provider updated successfully',
            'data' => new HealthcareProviderResource($provider),
        ]);
    }

    /**
     * Remove the specified healthcare provider.
     *
     * @param HealthcareProvider $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(HealthcareProvider $provider)
    {
        $provider->delete();

        // Clear cache
        $this->cacheService->clearTag('providers');

        return response()->json([
            'message' => 'Healthcare provider deleted successfully'
        ]);
    }
} 