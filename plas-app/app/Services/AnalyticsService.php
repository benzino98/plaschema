<?php

namespace App\Services;

use App\Models\News;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\HealthcareProvider;
use App\Models\Faq;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;

class AnalyticsService
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Get analytics summary for dashboard
     * 
     * @return array
     */
    public function getDashboardSummary()
    {
        return $this->cacheService->remember('analytics.dashboard.summary', function () {
            return [
                'totalProviders' => HealthcareProvider::count(),
                'totalNews' => News::count(),
                'totalFaqs' => Faq::count(),
                'totalMessages' => ContactMessage::count(),
                'newMessages' => ContactMessage::where('status', 'new')->count(),
                'totalUsers' => User::count(),
                'activityCount' => ActivityLog::count(),
                'activityToday' => ActivityLog::whereDate('created_at', Carbon::today())->count(),
                'providersByCategory' => $this->getProvidersByCategory(),
                'messagesByCategory' => $this->getMessagesByCategory(),
                'activityTimeline' => $this->getActivityTimeline(),
                'contentGrowth' => $this->getContentGrowth(),
                'messageStatistics' => $this->getMessageStatistics(),
            ];
        }, 60 * 24); // Cache for 24 hours
    }

    /**
     * Get providers grouped by category
     * 
     * @return array
     */
    public function getProvidersByCategory()
    {
        return $this->cacheService->remember('analytics.providers.by.category', function () {
            return DB::table('healthcare_providers')
                ->select(DB::raw('categories.name as category, count(*) as count'))
                ->join('categories', 'healthcare_providers.category_id', '=', 'categories.id')
                ->groupBy('categories.name')
                ->get()
                ->pluck('count', 'category')
                ->toArray();
        }, 60 * 24); // Cache for 24 hours
    }

    /**
     * Get messages grouped by category
     * 
     * @return array
     */
    public function getMessagesByCategory()
    {
        return $this->cacheService->remember('analytics.messages.by.category', function () {
            return DB::table('contact_messages')
                ->select(DB::raw('message_categories.name as category, count(*) as count'))
                ->join('message_categories', 'contact_messages.category_id', '=', 'message_categories.id')
                ->groupBy('message_categories.name')
                ->get()
                ->pluck('count', 'category')
                ->toArray();
        }, 60 * 24); // Cache for 24 hours
    }

    /**
     * Get activity timeline for the last 30 days
     * 
     * @return array
     */
    public function getActivityTimeline()
    {
        return $this->cacheService->remember('analytics.activity.timeline', function () {
            $startDate = Carbon::now()->subDays(30);
            $endDate = Carbon::now();
            
            return ActivityLog::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
        }, 60 * 24); // Cache for 24 hours
    }

    /**
     * Get content growth over time (monthly)
     * 
     * @return array
     */
    public function getContentGrowth()
    {
        return $this->cacheService->remember('analytics.content.growth', function () {
            // Get dates for last 12 months
            $months = collect(range(0, 11))->map(function ($i) {
                return Carbon::now()->startOfMonth()->subMonths($i);
            })->reverse();

            $news = $this->getMonthlyCreationCount('news', $months);
            $providers = $this->getMonthlyCreationCount('healthcare_providers', $months);
            $faqs = $this->getMonthlyCreationCount('faqs', $months);

            $result = [];
            foreach ($months as $month) {
                $monthKey = $month->format('M Y');
                $result[$monthKey] = [
                    'news' => $news[$monthKey] ?? 0,
                    'providers' => $providers[$monthKey] ?? 0,
                    'faqs' => $faqs[$monthKey] ?? 0,
                ];
            }

            return $result;
        }, 60 * 24); // Cache for 24 hours
    }

    /**
     * Get monthly creation count for a table
     * 
     * @param string $table
     * @param \Illuminate\Support\Collection $months
     * @return array
     */
    protected function getMonthlyCreationCount($table, $months)
    {
        $counts = DB::table($table)
            ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as count'))
            ->where('created_at', '>=', $months->first())
            ->groupBy('year', 'month')
            ->get();

        $result = [];
        foreach ($counts as $count) {
            $monthKey = Carbon::createFromDate($count->year, $count->month, 1)->format('M Y');
            $result[$monthKey] = $count->count;
        }
        
        // Fill in missing months with zero
        foreach ($months as $month) {
            $monthKey = $month->format('M Y');
            if (!isset($result[$monthKey])) {
                $result[$monthKey] = 0;
            }
        }

        return $result;
    }

    /**
     * Get message statistics by status, response time
     * 
     * @return array
     */
    public function getMessageStatistics()
    {
        return $this->cacheService->remember('analytics.messages.statistics', function () {
            return [
                'statusCounts' => DB::table('contact_messages')
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                
                'averageResponseTime' => $this->calculateAverageResponseTime(),
                
                'monthlyCounts' => DB::table('contact_messages')
                    ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as count'))
                    ->where('created_at', '>=', Carbon::now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                            'count' => $item->count
                        ];
                    })
                    ->pluck('count', 'month')
                    ->toArray(),
            ];
        }, 60 * 24); // Cache for 24 hours
    }

    /**
     * Calculate average response time in hours
     * 
     * @return float
     */
    protected function calculateAverageResponseTime()
    {
        $messages = ContactMessage::whereNotNull('responded_at')
            ->select('created_at', 'responded_at')
            ->get();

        if ($messages->isEmpty()) {
            return 0;
        }

        $totalHours = 0;
        foreach ($messages as $message) {
            $created = Carbon::parse($message->created_at);
            $responded = Carbon::parse($message->responded_at);
            $totalHours += $created->diffInHours($responded);
        }

        return round($totalHours / $messages->count(), 1);
    }

    /**
     * Generate analytics report for export
     * 
     * @param string $type (summary, providers, messages, activity)
     * @param array $options
     * @return array
     */
    public function generateReport($type, array $options = [])
    {
        $startDate = isset($options['start_date']) ? Carbon::parse($options['start_date']) : Carbon::now()->subDays(30);
        $endDate = isset($options['end_date']) ? Carbon::parse($options['end_date']) : Carbon::now();

        switch ($type) {
            case 'summary':
                return $this->generateSummaryReport($startDate, $endDate);
            case 'providers':
                return $this->generateProviderReport($startDate, $endDate);
            case 'messages':
                return $this->generateMessageReport($startDate, $endDate);
            case 'activity':
                return $this->generateActivityReport($startDate, $endDate, $options['user_id'] ?? null);
            default:
                return [];
        }
    }

    /**
     * Generate summary report
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    protected function generateSummaryReport($startDate, $endDate)
    {
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'content' => [
                'new_providers' => HealthcareProvider::whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_news' => News::whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_faqs' => Faq::whereBetween('created_at', [$startDate, $endDate])->count(),
            ],
            'messages' => [
                'total' => ContactMessage::whereBetween('created_at', [$startDate, $endDate])->count(),
                'by_status' => DB::table('contact_messages')
                    ->select('status', DB::raw('count(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
            ],
            'activity' => [
                'total' => ActivityLog::whereBetween('created_at', [$startDate, $endDate])->count(),
                'by_action' => DB::table('activity_logs')
                    ->select('action', DB::raw('count(*) as count'))
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('action')
                    ->pluck('count', 'action')
                    ->toArray(),
            ],
        ];
    }

    /**
     * Generate provider report
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    protected function generateProviderReport($startDate, $endDate)
    {
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'total' => HealthcareProvider::whereBetween('created_at', [$startDate, $endDate])->count(),
            'by_category' => DB::table('healthcare_providers')
                ->select('categories.name as category', DB::raw('count(*) as count'))
                ->join('categories', 'healthcare_providers.category_id', '=', 'categories.id')
                ->whereBetween('healthcare_providers.created_at', [$startDate, $endDate])
                ->groupBy('categories.name')
                ->pluck('count', 'category')
                ->toArray(),
            'by_type' => DB::table('healthcare_providers')
                ->select('type', DB::raw('count(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_city' => DB::table('healthcare_providers')
                ->select(DB::raw('SUBSTRING_INDEX(address, ",", -1) as city'), DB::raw('count(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('city')
                ->pluck('count', 'city')
                ->toArray(),
        ];
    }

    /**
     * Generate message report
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    protected function generateMessageReport($startDate, $endDate)
    {
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'total' => ContactMessage::whereBetween('created_at', [$startDate, $endDate])->count(),
            'by_category' => DB::table('contact_messages')
                ->select('message_categories.name as category', DB::raw('count(*) as count'))
                ->join('message_categories', 'contact_messages.category_id', '=', 'message_categories.id')
                ->whereBetween('contact_messages.created_at', [$startDate, $endDate])
                ->groupBy('message_categories.name')
                ->pluck('count', 'category')
                ->toArray(),
            'by_status' => DB::table('contact_messages')
                ->select('status', DB::raw('count(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'response_times' => $this->getResponseTimesForPeriod($startDate, $endDate),
            'daily_counts' => DB::table('contact_messages')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray(),
        ];
    }

    /**
     * Get response times for a specific period
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    protected function getResponseTimesForPeriod($startDate, $endDate)
    {
        $messages = ContactMessage::whereNotNull('responded_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('created_at', 'responded_at')
            ->get();

        if ($messages->isEmpty()) {
            return [
                'average_hours' => 0,
                'min_hours' => 0,
                'max_hours' => 0,
            ];
        }

        $hours = [];
        foreach ($messages as $message) {
            $created = Carbon::parse($message->created_at);
            $responded = Carbon::parse($message->responded_at);
            $hours[] = $created->diffInHours($responded);
        }

        return [
            'average_hours' => round(array_sum($hours) / count($hours), 1),
            'min_hours' => min($hours),
            'max_hours' => max($hours),
        ];
    }

    /**
     * Generate activity report
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $userId
     * @return array
     */
    protected function generateActivityReport($startDate, $endDate, $userId = null)
    {
        $query = ActivityLog::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'total' => $query->count(),
            'by_user' => DB::table('activity_logs')
                ->select('users.name as user', DB::raw('count(*) as count'))
                ->join('users', 'activity_logs.user_id', '=', 'users.id')
                ->whereBetween('activity_logs.created_at', [$startDate, $endDate])
                ->groupBy('users.name')
                ->pluck('count', 'user')
                ->toArray(),
            'by_action' => DB::table('activity_logs')
                ->select('action', DB::raw('count(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($userId, function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
            'by_entity_type' => DB::table('activity_logs')
                ->select('entity_type', DB::raw('count(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($userId, function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })
                ->groupBy('entity_type')
                ->pluck('count', 'entity_type')
                ->toArray(),
            'daily_counts' => DB::table('activity_logs')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($userId, function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray(),
        ];
    }
} 