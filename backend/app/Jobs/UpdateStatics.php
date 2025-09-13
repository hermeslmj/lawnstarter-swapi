<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\StatisticsService;

class UpdateStatics implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue;

    public function __construct(){}

    public function handle(StatisticsService $statisticsService): void
    {
        $statisticsService->generateStatistics();
    }
}
