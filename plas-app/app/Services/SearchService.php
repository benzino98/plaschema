<?php

namespace App\Services;

use App\Models\HealthcareProvider;
use App\Models\News;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Search across different entity types based on criteria
     *
     * @param string|null $term
     * @param string $type
     * @param int|null $category
     * @param string|null $location
     * @param array $options
     * @return array
     */
    public function search(?string $term, string $type = 'all', ?int $category = null, ?string $location = null, array $options = [])
    {
        $results = [];
        $perPage = $options['per_page'] ?? 10;
        $page = $options['page'] ?? 1;
        
        switch ($type) {
            case 'providers':
                $results = $this->searchProviders($term, $category, $location, $perPage, $page);
                break;
            case 'news':
                $results = $this->searchNews($term, $category, $perPage, $page);
                break;
            case 'faqs':
                $results = $this->searchFaqs($term, $category, $perPage, $page);
                break;
            case 'all':
            default:
                $results = $this->searchAll($term, $category, $location, $perPage, $page);
                break;
        }
        
        return $results;
    }
    
    /**
     * Search healthcare providers
     *
     * @param string|null $term
     * @param int|null $category
     * @param string|null $location
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function searchProviders(?string $term, ?int $category = null, ?string $location = null, int $perPage = 10, int $page = 1)
    {
        $query = HealthcareProvider::query();
        
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                    ->orWhere('description', 'LIKE', "%{$term}%")
                    ->orWhere('address', 'LIKE', "%{$term}%")
                    ->orWhere('specialties', 'LIKE', "%{$term}%");
            });
        }
        
        if ($category) {
            $query->where('category_id', $category);
        }
        
        if ($location) {
            $query->where('city', $location);
        }
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
    
    /**
     * Search news articles
     *
     * @param string|null $term
     * @param int|null $category
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function searchNews(?string $term, ?int $category = null, int $perPage = 10, int $page = 1)
    {
        $query = News::query()
            ->where('published_at', '<=', now());
        
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('title', 'LIKE', "%{$term}%")
                    ->orWhere('content', 'LIKE', "%{$term}%");
            });
        }
        
        if ($category) {
            $query->where('category_id', $category);
        }
        
        return $query->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
    
    /**
     * Search FAQs
     *
     * @param string|null $term
     * @param int|null $category
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function searchFaqs(?string $term, ?int $category = null, int $perPage = 10, int $page = 1)
    {
        $query = Faq::query();
        
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('question', 'LIKE', "%{$term}%")
                    ->orWhere('answer', 'LIKE', "%{$term}%");
            });
        }
        
        if ($category) {
            $query->where('category_id', $category);
        }
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
    
    /**
     * Search across all entity types
     *
     * @param string|null $term
     * @param int|null $category
     * @param string|null $location
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function searchAll(?string $term, ?int $category = null, ?string $location = null, int $perPage = 10, int $page = 1)
    {
        $providers = $this->searchProviders($term, $category, $location, $perPage);
        $news = $this->searchNews($term, $category, $perPage);
        $faqs = $this->searchFaqs($term, $category, $perPage);
        
        return [
            'providers' => $providers,
            'news' => $news,
            'faqs' => $faqs,
            'total_count' => $providers->total() + $news->total() + $faqs->total()
        ];
    }
} 