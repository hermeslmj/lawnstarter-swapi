<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;
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
        $start = microtime(true);
        
        try {
            $serviceResponse = $this->peopleService->getPeopleBySearch($request->query('searchTerm', ''));
            
            $end = microtime(true);
            $executionTime = $end - $start;

            $queryLog = new QueryLogs();
            $queryLog->query = $request->fullUrl();
            $queryLog->execution_time = number_format($executionTime, 5);
            $queryLog->save();

            if (!$serviceResponse->success) {
                return ApiResponse::error($serviceResponse->message, $serviceResponse->code);
            }

            return ApiResponse::success($serviceResponse->data);
        } catch (\Exception $e) {
            return ApiResponse::error('An unexpected error occurred', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $start = microtime(true);
        
        try {
            $serviceResponse = $this->peopleService->getPersonById($request->query('id', ''));
            
            $end = microtime(true);
            $executionTime = $end - $start;

            $queryLog = new QueryLogs();
            $queryLog->query = $request->fullUrl();
            $queryLog->execution_time = number_format($executionTime, 5);
            $queryLog->save();

            if (!$serviceResponse->success) {
                return ApiResponse::error($serviceResponse->message, $serviceResponse->code);
            }

            return ApiResponse::success($serviceResponse->data);
        } catch (\Exception $e) {
            return ApiResponse::error('An unexpected error occurred', 500);
        }
    }
}