<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\HealthcareProvider;
use App\Models\News;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Role;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $analyticsService;
    protected $cacheService;

    /**
     * Create a new controller instance.
     *
     * @param AnalyticsService $analyticsService
     * @param CacheService $cacheService
     */
    public function __construct(AnalyticsService $analyticsService, CacheService $cacheService)
    {
        $this->analyticsService = $analyticsService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display the admin dashboard with enhanced metrics and visualizations.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Clear the dashboard cache to ensure we see the latest activity logs
        $this->cacheService->forget('admin.dashboard.data');
        
        // Cache dashboard data for 5 minutes to optimize performance but still see recent changes
        $dashboardData = $this->cacheService->remember('admin.dashboard.data', 5 * 60, function () {
            return [
                // Group 1: Content Management Metrics
                'content' => [
                    'news' => [
                        'total' => News::count(),
                        'published' => News::published()->count(),
                        'featured' => News::featured()->count(),
                        'recent' => News::published()->orderBy('published_at', 'desc')->limit(5)->get(),
                    ],
                    'providers' => [
                        'total' => HealthcareProvider::count(),
                        'active' => HealthcareProvider::active()->count(),
                        'by_type' => $this->getProvidersByType(),
                        'recent' => HealthcareProvider::orderBy('created_at', 'desc')->limit(5)->get(),
                    ],
                    'faqs' => [
                        'total' => Faq::count(),
                        'active' => Faq::active()->count(),
                        'by_category' => $this->getFaqsByCategory(),
                    ],
                    'resources' => [
                        'total' => Resource::count(),
                        'active' => Resource::active()->count(),
                        'download_count' => Resource::sum('download_count'),
                        'most_downloaded' => Resource::orderBy('download_count', 'desc')->limit(5)->get(),
                        'by_type' => $this->getResourcesByType(),
                    ],
                ],

                // Group 2: User Management Metrics
                'users' => [
                    'total' => User::count(),
                    'by_role' => $this->getUsersByRole(),
                    'recent' => User::orderBy('created_at', 'desc')->limit(5)->get(),
                ],

                // Group 3: Activity & Communications
                'activity' => [
                    'total' => ActivityLog::count(),
                    'today' => ActivityLog::whereDate('created_at', Carbon::today())->count(),
                    // Always fetch the most recent activity logs directly without caching
                    'recent' => ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get(),
                    'by_action' => $this->getActivityByAction(),
                ],
                'messages' => [
                    'total' => ContactMessage::count(),
                    'new' => ContactMessage::where('status', 'new')->count(),
                    'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
                    'resolved' => ContactMessage::where('status', 'resolved')->count(),
                    'recent' => ContactMessage::orderBy('created_at', 'desc')->limit(5)->get(),
                ],

                // Group 4: Charts & Visualizations Data
                'charts' => [
                    'content_growth' => $this->getContentGrowthData(),
                    'activity_timeline' => $this->getActivityTimelineData(),
                    'provider_distribution' => $this->getProvidersByType(),
                    'download_statistics' => $this->getDownloadStatistics(),
                ],
            ];
        });

        return view('admin.dashboard', compact('dashboardData'));
    }

    /**
     * Get providers grouped by type.
     *
     * @return array
     */
    private function getProvidersByType()
    {
        return HealthcareProvider::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Get FAQs grouped by category.
     *
     * @return array
     */
    private function getFaqsByCategory()
    {
        return Faq::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Get resources grouped by file type.
     *
     * @return array
     */
    private function getResourcesByType()
    {
        return Resource::select('file_type', DB::raw('count(*) as count'))
            ->groupBy('file_type')
            ->pluck('count', 'file_type')
            ->toArray();
    }

    /**
     * Get users grouped by role.
     *
     * @return array
     */
    private function getUsersByRole()
    {
        $roles = Role::with('users')->get();
        $data = [];

        foreach ($roles as $role) {
            $data[$role->name] = $role->users->count();
        }

        return $data;
    }

    /**
     * Get activity logs grouped by action.
     *
     * @return array
     */
    private function getActivityByAction()
    {
        return ActivityLog::select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();
    }

    /**
     * Get content growth data for charts (last 6 months).
     *
     * @return array
     */
    private function getContentGrowthData()
    {
        // Get dates for last 6 months
        $months = collect(range(0, 5))->map(function ($i) {
            return Carbon::now()->startOfMonth()->subMonths($i);
        })->reverse();

        $news = [];
        $providers = [];
        $faqs = [];
        $resources = [];
        $labels = [];

        foreach ($months as $month) {
            $monthLabel = $month->format('M Y');
            $labels[] = $monthLabel;
            
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $news[] = News::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $providers[] = HealthcareProvider::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $faqs[] = Faq::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $resources[] = Resource::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'News', 'data' => $news],
                ['label' => 'Providers', 'data' => $providers],
                ['label' => 'FAQs', 'data' => $faqs],
                ['label' => 'Resources', 'data' => $resources],
            ],
        ];
    }

    /**
     * Get activity timeline data for charts (last 14 days).
     *
     * @return array
     */
    private function getActivityTimelineData()
    {
        $days = collect(range(0, 13))->map(function ($i) {
            return Carbon::now()->subDays($i);
        })->reverse();

        $counts = [];
        $labels = [];

        foreach ($days as $day) {
            $dayLabel = $day->format('M d');
            $labels[] = $dayLabel;
            
            $counts[] = ActivityLog::whereDate('created_at', $day->toDateString())->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Activities', 'data' => $counts],
            ],
        ];
    }

    /**
     * Get download statistics data for charts.
     *
     * @return array
     */
    private function getDownloadStatistics()
    {
        // Get resource categories with their total downloads
        $categories = ResourceCategory::with('resources')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'downloads' => $category->resources->sum('download_count'),
                ];
            })
            ->sortByDesc('downloads')
            ->take(5);

        return [
            'labels' => $categories->pluck('name')->toArray(),
            'datasets' => [
                ['label' => 'Downloads', 'data' => $categories->pluck('downloads')->toArray()],
            ],
        ];
    }
} 