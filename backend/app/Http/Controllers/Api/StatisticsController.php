<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StatisticsService;

class StatisticsController extends Controller
{
    protected $statisticsService;
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index()
    {
        return response()->json($this->statisticsService->getAllStatistics());
    }
}