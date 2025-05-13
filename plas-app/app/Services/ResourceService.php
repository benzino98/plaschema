<?php

namespace App\Services;

use App\Models\Resource;
use App\Repositories\Contracts\ResourceRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordParser;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetParser;
use Exception;

class ResourceService
{
    /**
     * @var ResourceRepositoryInterface
     */
    protected $resourceRepository;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * @var ActivityLogService
     */
    protected $activityLogService;

    /**
     * ResourceService constructor.
     *
     * @param ResourceRepositoryInterface $resourceRepository
     * @param CacheService $cacheService
     * @param ActivityLogService $activityLogService
     */
    public function __construct(
        ResourceRepositoryInterface $resourceRepository,
        CacheService $cacheService,
        ActivityLogService $activityLogService
    ) {
        $this->resourceRepository = $resourceRepository;
        $this->cacheService = $cacheService;
        $this->activityLogService = $activityLogService;
    }

    /**
     * Get all resources.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $filters = [])
    {
        $cacheKey = Resource::collectionCacheKey($filters);
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($filters) {
            return $this->resourceRepository->getAll($filters);
        });
    }

    /**
     * Get paginated resources.
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $filters = [])
    {
        $cacheKey = Resource::collectionCacheKey(array_merge($filters, ['page' => request()->get('page', 1), 'perPage' => $perPage]));
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($perPage, $filters) {
            return $this->resourceRepository->getPaginated($perPage, $filters);
        });
    }

    /**
     * Get a resource by ID.
     *
     * @param int $id
     * @return Resource|null
     */
    public function getById(int $id)
    {
        $cacheKey = "resource_{$id}";
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
            return $this->resourceRepository->getById($id);
        });
    }

    /**
     * Get a resource by slug.
     *
     * @param string $slug
     * @return Resource|null
     */
    public function getBySlug(string $slug)
    {
        $cacheKey = "resource_slug_{$slug}";
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($slug) {
            return $this->resourceRepository->getBySlug($slug);
        });
    }

    /**
     * Create a new resource.
     *
     * @param array $data
     * @param UploadedFile $file
     * @return Resource
     */
    public function create(array $data, UploadedFile $file)
    {
        // Store the file
        $filePath = $this->storeFile($file);
        
        // Extract searchable content
        $searchableContent = $this->extractSearchableContent($file);
        
        // Prepare resource data
        $resourceData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
            'searchable_content' => $searchableContent,
            'publish_date' => $data['publish_date'] ?? now(),
            'is_featured' => $data['is_featured'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ];
        
        // Create the resource
        $resource = $this->resourceRepository->create($resourceData);
        
        // Log activity
        $this->activityLogService->log(
            'created',
            'resource',
            $resource->id,
            "Created resource: {$resource->title}"
        );
        
        // Clear cache
        $this->clearResourceCache();
        
        return $resource;
    }

    /**
     * Update a resource.
     *
     * @param int $id
     * @param array $data
     * @param UploadedFile|null $file
     * @return Resource
     */
    public function update(int $id, array $data, ?UploadedFile $file = null)
    {
        $resource = $this->getById($id);
        
        if (!$resource) {
            throw new Exception('Resource not found');
        }
        
        $resourceData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? $resource->description,
            'category_id' => $data['category_id'] ?? $resource->category_id,
            'publish_date' => $data['publish_date'] ?? $resource->publish_date,
            'is_featured' => $data['is_featured'] ?? $resource->is_featured,
            'is_active' => $data['is_active'] ?? $resource->is_active,
        ];
        
        // Process file upload if a new file is provided
        if ($file) {
            // Delete old file
            $this->deleteFile($resource->file_path);
            
            // Store new file
            $filePath = $this->storeFile($file);
            
            // Extract searchable content
            $searchableContent = $this->extractSearchableContent($file);
            
            // Update file-related data
            $resourceData['file_path'] = $filePath;
            $resourceData['file_name'] = $file->getClientOriginalName();
            $resourceData['file_size'] = $file->getSize();
            $resourceData['file_type'] = $file->getMimeType();
            $resourceData['searchable_content'] = $searchableContent;
        }
        
        // Update the resource
        $resource = $this->resourceRepository->update($id, $resourceData);
        
        // Log activity
        $this->activityLogService->log(
            'updated',
            'resource',
            $resource->id,
            "Updated resource: {$resource->title}"
        );
        
        // Clear cache
        $this->clearResourceCache($id);
        
        return $resource;
    }

    /**
     * Delete a resource.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $resource = $this->getById($id);
        
        if (!$resource) {
            return false;
        }
        
        // Delete the file
        $this->deleteFile($resource->file_path);
        
        // Delete the resource
        $result = $this->resourceRepository->delete($id);
        
        if ($result) {
            // Log activity
            $this->activityLogService->log(
                'deleted',
                'resource',
                $id,
                "Deleted resource: {$resource->title}"
            );
            
            // Clear cache
            $this->clearResourceCache($id);
        }
        
        return $result;
    }

    /**
     * Increment download count for a resource.
     *
     * @param int $id
     * @return bool
     */
    public function incrementDownloadCount(int $id)
    {
        $result = $this->resourceRepository->incrementDownloadCount($id);
        
        if ($result) {
            // Clear cache for this resource
            $this->cacheService->forget("resource_{$id}");
        }
        
        return $result;
    }

    /**
     * Get the file storage path for a resource.
     *
     * @param int $id
     * @return string|null
     */
    public function getFilePath(int $id)
    {
        $resource = $this->getById($id);
        
        if (!$resource) {
            return null;
        }
        
        return $resource->file_path;
    }

    /**
     * Download a resource file.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function downloadFile(int $id)
    {
        $resource = $this->getById($id);
        
        if (!$resource) {
            return null;
        }
        
        // Increment download count
        $this->incrementDownloadCount($id);
        
        // Generate download response
        return Storage::download($resource->file_path, $resource->file_name);
    }

    /**
     * Get featured resources.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeatured(int $limit = 5)
    {
        $cacheKey = "resources_featured_{$limit}";
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($limit) {
            return $this->resourceRepository->getFeatured($limit);
        });
    }

    /**
     * Get most downloaded resources.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMostDownloaded(int $limit = 5)
    {
        $cacheKey = "resources_most_downloaded_{$limit}";
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($limit) {
            return $this->resourceRepository->getMostDownloaded($limit);
        });
    }

    /**
     * Search resources.
     *
     * @param string $term
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $term, int $perPage = 15)
    {
        $cacheKey = "resources_search_" . md5($term) . "_{$perPage}_" . request()->get('page', 1);
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($term, $perPage) {
            return $this->resourceRepository->search($term, $perPage);
        });
    }

    /**
     * Get resources by category.
     *
     * @param int $categoryId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 15)
    {
        $cacheKey = "resources_category_{$categoryId}_{$perPage}_" . request()->get('page', 1);
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($categoryId, $perPage) {
            return $this->resourceRepository->getByCategory($categoryId, $perPage);
        });
    }

    /**
     * Store file.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function storeFile(UploadedFile $file)
    {
        // Generate a unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Store the file in the resources directory
        $path = $file->storeAs('resources', $filename, 'public');
        
        return $path;
    }

    /**
     * Delete file.
     *
     * @param string $filePath
     * @return bool
     */
    protected function deleteFile(string $filePath)
    {
        if (Storage::exists($filePath)) {
            return Storage::delete($filePath);
        }
        
        return false;
    }

    /**
     * Extract searchable content from file.
     *
     * @param UploadedFile $file
     * @return string|null
     */
    protected function extractSearchableContent(UploadedFile $file)
    {
        $mimeType = $file->getMimeType();
        $content = null;
        
        try {
            // Extract content based on file type
            if (Str::contains($mimeType, 'pdf')) {
                $content = $this->extractPdfContent($file);
            } elseif (Str::contains($mimeType, 'word') || $file->getClientOriginalExtension() === 'docx') {
                $content = $this->extractWordContent($file);
            } elseif (Str::contains($mimeType, 'spreadsheet') || Str::contains($mimeType, 'excel')) {
                $content = $this->extractSpreadsheetContent($file);
            } elseif (Str::contains($mimeType, 'text')) {
                $content = file_get_contents($file->getRealPath());
            }
        } catch (Exception $e) {
            Log::error('Error extracting content from file: ' . $e->getMessage());
        }
        
        // Limit content length to avoid database issues (e.g., 16MB for MySQL)
        if ($content) {
            $content = Str::limit($content, 65535); // TEXT column limit
        }
        
        return $content;
    }

    /**
     * Extract content from PDF file.
     *
     * @param UploadedFile $file
     * @return string|null
     */
    protected function extractPdfContent(UploadedFile $file)
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getRealPath());
            
            return $pdf->getText();
        } catch (Exception $e) {
            Log::error('Error extracting PDF content: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract content from Word document.
     *
     * @param UploadedFile $file
     * @return string|null
     */
    protected function extractWordContent(UploadedFile $file)
    {
        try {
            $phpWord = WordParser::load($file->getRealPath());
            $text = '';
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    }
                }
            }
            
            return $text;
        } catch (Exception $e) {
            Log::error('Error extracting Word content: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract content from spreadsheet.
     *
     * @param UploadedFile $file
     * @return string|null
     */
    protected function extractSpreadsheetContent(UploadedFile $file)
    {
        try {
            $spreadsheet = SpreadsheetParser::load($file->getRealPath());
            $text = '';
            
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                foreach ($worksheet->getRowIterator() as $row) {
                    foreach ($row->getCellIterator() as $cell) {
                        $text .= $cell->getValue() . ' ';
                    }
                }
            }
            
            return $text;
        } catch (Exception $e) {
            Log::error('Error extracting Spreadsheet content: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear all resource caches.
     *
     * @param int|null $resourceId
     * @return void
     */
    protected function clearResourceCache(?int $resourceId = null)
    {
        // Clear specific resource cache if ID is provided
        if ($resourceId) {
            $this->cacheService->forget("resource_{$resourceId}");
            
            // Get resource to clear slug cache
            $resource = $this->resourceRepository->getById($resourceId);
            if ($resource) {
                $this->cacheService->forget("resource_slug_{$resource->slug}");
            }
        }
        
        // Clear collection caches
        $this->cacheService->deleteByPattern('resources_*');
    }

    /**
     * Get public resources with pagination and filtering.
     *
     * @param string|null $search
     * @param int|null $categoryId
     * @param bool|null $featured
     * @param int $perPage
     * @param string $orderBy
     * @param string $direction
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPublicResourcesPaginated(
        ?string $search = null,
        ?int $categoryId = null,
        ?bool $featured = null,
        int $perPage = 15,
        string $orderBy = 'created_at',
        string $direction = 'desc'
    ) {
        $cacheKey = "resources_public_" . 
            ($search ? md5($search) . "_" : "") . 
            ($categoryId ? "cat{$categoryId}_" : "") . 
            ($featured ? "featured_" : "") . 
            "{$perPage}_{$orderBy}_{$direction}_" . 
            request()->get('page', 1);
        
        return $this->cacheService->remember($cacheKey, 3600, function () use (
            $search, $categoryId, $featured, $perPage, $orderBy, $direction
        ) {
            $filters = [
                'published' => true,
                'search' => $search,
                'category_id' => $categoryId,
                'featured' => $featured,
                'order_by' => $orderBy,
                'direction' => $direction
            ];
            
            return $this->resourceRepository->getPaginated($perPage, $filters);
        });
    }

    /**
     * Get featured resources.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedResources(int $limit = 5)
    {
        $cacheKey = "resources_featured_limit_{$limit}";
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($limit) {
            return $this->resourceRepository->getByFilters([
                'published' => true,
                'featured' => true,
                'limit' => $limit,
                'order_by' => 'created_at',
                'direction' => 'desc'
            ]);
        });
    }

    /**
     * Get resources related to the given resource.
     *
     * @param Resource $resource
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedResources($resource, int $limit = 3)
    {
        $cacheKey = "resources_related_{$resource->id}_{$limit}";
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($resource, $limit) {
            return $this->resourceRepository->getRelated($resource->id, $resource->category_id, $limit);
        });
    }

    /**
     * Get resources by category with pagination.
     *
     * @param int $categoryId
     * @param int $perPage
     * @param string $orderBy
     * @param string $direction
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getResourcesByCategory(
        int $categoryId,
        int $perPage = 15,
        string $orderBy = 'created_at',
        string $direction = 'desc'
    ) {
        $cacheKey = "resources_by_category_{$categoryId}_{$perPage}_{$orderBy}_{$direction}_" . 
            request()->get('page', 1);
        
        return $this->cacheService->remember($cacheKey, 3600, function () use (
            $categoryId, $perPage, $orderBy, $direction
        ) {
            $filters = [
                'published' => true,
                'category_id' => $categoryId,
                'order_by' => $orderBy,
                'direction' => $direction
            ];
            
            return $this->resourceRepository->getPaginated($perPage, $filters);
        });
    }

    /**
     * Download a resource file.
     *
     * @param Resource $resource
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadResource($resource)
    {
        // Increment download count
        $this->incrementDownloadCount($resource->id);
        
        // Log activity
        $this->activityLogService->log(
            'downloaded',
            'resource',
            $resource->id,
            "Downloaded resource: {$resource->title}"
        );
        
        // Generate download response
        return Storage::download($resource->file_path, $resource->file_name);
    }
} 