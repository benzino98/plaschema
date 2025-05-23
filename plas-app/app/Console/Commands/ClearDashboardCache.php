<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the dashboard cache to ensure recent activities are displayed';

    /**
     * The cache service instance.
     *
     * @var \App\Services\CacheService
     */
    protected $cacheService;

    /**
     * Create a new command instance.
     *
     * @param CacheService $cacheService
     * @return void
     */
    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->cacheService->forget('admin.dashboard.data');
        $this->cacheService->deleteByPattern('analytics.*');
        
        $this->info('Dashboard and analytics cache cleared successfully!');
        $this->info('Recent activity logs should now be visible on the dashboard.');
        
        return 0;
    }
} 