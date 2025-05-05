<?php

namespace App\Console\Commands;

use App\Services\ContactMessageService;
use Illuminate\Console\Command;

class ArchiveOldMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:archive-old-messages {--months=3 : Number of months old a message must be to be archived}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive contact messages that are older than the specified number of months';

    /**
     * The contact message service instance.
     *
     * @var ContactMessageService
     */
    protected $contactMessageService;

    /**
     * Create a new command instance.
     *
     * @param ContactMessageService $contactMessageService
     */
    public function __construct(ContactMessageService $contactMessageService)
    {
        parent::__construct();
        $this->contactMessageService = $contactMessageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = (int)$this->option('months');
        
        $this->info("Starting archival of messages older than {$months} months...");
        
        try {
            $count = $this->contactMessageService->runAutoArchiving($months);
            
            if ($count > 0) {
                $this->info("Successfully archived {$count} old contact message(s).");
            } else {
                $this->info("No messages found that needed archiving.");
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to archive old messages: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
