<?php

namespace App\Services;

class StatisticsService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function generateStatistics()
    {
        $stdout = fopen('php://stdout', 'w');
        fputs($stdout, "This message is to show queued job workin.\n");
        fclose($stdout);
    }

}
