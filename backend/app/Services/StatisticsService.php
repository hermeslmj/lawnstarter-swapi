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
            $existing->fill([
                'value' => QueryLogs::orderByDesc('execution_time')
                    ->limit(5)
                    ->get() ?? []
            ]);
            $existing->save();
            return;
        }

        Statistics::create([
            'description' => 'slowQueries',
            'title' => 'Top 5 Slow Queries',
            'value' => QueryLogs::orderByDesc('execution_time')
                ->limit(5)
                ->get() ?? []
        ]);
    }


    private function _updateTopFiveMostFrequentRequests()
    {
        $topRequests = QueryLogs::select('query', DB::raw('count(*) as count'))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(5)
            ->get() ?? [];

        $existing = Statistics::where('description', 'mostFrequentRequests')->first();
        if($existing){
            $existing->fill([
                'value' => $topRequests
            ]);
            $existing->save();
            return;
        }

        Statistics::create([
            'description' => 'mostFrequentRequests',
            'title' => 'Top 5 Most Frequent Requests',
            'value' => $topRequests
        ]);
    }
    
    private function _updateAverageExecutionTime()
    {
        $averageTime = QueryLogs::avg('execution_time') ?? 0;
        $existing = Statistics::where('description', 'averageExecutionTime')->first();
        if($existing){
            $existing->fill([
                'value' => $averageTime
            ]);
            $existing->save();
            return;
        }

        Statistics::create([
            'description' => 'averageExecutionTime',
            'title' => 'Average Execution Time',
            'value' => $averageTime
        ]);
    }   
}
