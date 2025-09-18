<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SearchService;
use App\Http\Response\ApiResponse;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $entity = $request->query('entity');
        $term = $request->query('term');

        if (!$entity || !$term) {
            return ApiResponse::error('Missing entity or term parameter', 400);
        }

        $response = $this->searchService->search($entity, $term);

        if (!$response->success) {
            return ApiResponse::error($response->message, $response->code ?? 400);
        }

        return ApiResponse::success($response->data);
    }

    public function show(Request $request)
    {
        $id = $request->query('id');
        $entity = $request->query('entity');

        if (!$entity) {
            return ApiResponse::error('Missing entity parameter', 400);
        }

        $response = $this->searchService->searchById($entity, $id);

        if (!$response->success) {
            return ApiResponse::error($response->message, $response->code ?? 400);
        }

        return ApiResponse::success($response->data);
    }
}
