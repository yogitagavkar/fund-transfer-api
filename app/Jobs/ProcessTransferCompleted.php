<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessTransferCompleted implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $transferData)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         Log::info(
            'Transfer processed asynchronously',
            $this->transferData
        );

        // Future:
        // Send email
        // Send SMS
    }
}
