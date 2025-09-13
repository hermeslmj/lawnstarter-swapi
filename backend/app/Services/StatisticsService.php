<?php

namespace App\Services;

use App\Models\QueryLogs;
use App\Models\Statistics;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function __construct(){}

    public function generateStatistics()
    {
        $this->_updateTopFiveSlowQueries();
        $this->_updateTopFiveMostFrequentRequests();
        $this->_updateAverageExecutionTime();
    }

    public function getAllStatistics()
    {
        return Statistics::all();
    }

    private function _updateTopFiveSlowQueries(){
        $existing = Statistics::where('description', 'slowQueries')->first();
        if($existing){
            $existing->value = QueryLogs::orderByDesc('execution_time')
                ->limit(5)
                ->get();
            $existing->save();
            return;
        }

        $statistics = new Statistics();
        $statistics->description = "slowQueries";
        $statistics->title = "Top 5 Slow Queries";
        $statistics->value = QueryLogs::orderByDesc('execution_time')
            ->limit(5)
            ->get();        
        $statistics->save();
    }


    private function _updateTopFiveMostFrequentRequests()
    {
        $topRequests = QueryLogs::select('query', DB::raw('count(*) as count'))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $existing = Statistics::where('description', 'mostFrequentRequests')->first();
        if($existing){
            $existing->value = $topRequests;
            $existing->save();
            return;
        }

        $statistics = new Statistics();
        $statistics->description = "mostFrequentRequests";
        $statistics->title = "Top 5 Most Frequent Requests";
        $statistics->value = $topRequests;
        $statistics->save();
    }
    
    private function _updateAverageExecutionTime()
    {
        $averageTime = QueryLogs::avg('execution_time');

        $existing = Statistics::where('description', 'averageExecutionTime')->first();
        if($existing){
            $existing->value = $averageTime;
            $existing->save();
            return;
        }

        $statistics = new Statistics();
        $statistics->description = "averageExecutionTime";
        $statistics->title = "Average Execution Time";
        $statistics->value = $averageTime;
        $statistics->save();
    }   
}
