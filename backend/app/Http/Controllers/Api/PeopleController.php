<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\QueryLogs;
use App\Services\PeopleService;


class PeopleController extends Controller
{
    protected $peopleService;

    public function __construct(PeopleService $peopleService)
    {
        $this->peopleService = $peopleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $queryLog = new QueryLogs();
        $start = microtime(true);
        $response = $this->peopleService->getPeopleBySearch($request->query('searchTerm', ''));
        $end = microtime(true);
        $executionTime = $end - $start;

        $queryLog->query = $request->fullUrl();;
        $queryLog->execution_time = number_format($executionTime, 5);
        $queryLog->save();
        
        
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $queryLog = new QueryLogs();
        $start = microtime(true);
        $response = $this->peopleService->getPersonById($request->query('id',''));
        $end = microtime(true);
        $executionTime = $end - $start;
        $queryLog->query = $request->fullUrl();
        $queryLog->execution_time = number_format($executionTime, 5);
        $queryLog->save();
        return response()->json($response);
    }
}