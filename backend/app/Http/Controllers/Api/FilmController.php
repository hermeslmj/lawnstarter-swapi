<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;
use Illuminate\Http\Request;
use App\Services\FilmService;
use App\Models\QueryLogs;

class FilmController extends Controller
{
    protected $filmService;

    public function __construct(FilmService $filmService)
    {
        $this->filmService = $filmService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = microtime(true);
        
        try {
            $serviceResponse = $this->filmService->getFilmsBySearch($request->query('searchTerm', ''));
            
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
            $serviceResponse = $this->filmService->getFilmById($request->query('id', ''));
            
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
