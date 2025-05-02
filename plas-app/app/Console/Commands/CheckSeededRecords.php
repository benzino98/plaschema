<?php

namespace App\Console\Commands;

use App\Models\Faq;
use App\Models\HealthcareProvider;
use App\Models\News;
use App\Models\User;
use Illuminate\Console\Command;

class CheckSeededRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the number of seeded records in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userCount = User::count();
        $providerCount = HealthcareProvider::count();
        $newsCount = News::count();
        $faqCount = Faq::count();

        $this->info('Database Record Counts:');
        $this->info("Users: {$userCount}");
        $this->info("Healthcare Providers: {$providerCount}");
        $this->info("News Articles: {$newsCount}");
        $this->info("FAQs: {$faqCount}");

        return 0;
    }
} 