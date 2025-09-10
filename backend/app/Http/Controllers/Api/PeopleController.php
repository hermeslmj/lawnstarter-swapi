<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $response = $this->peopleService->getPeopleBySearch($request->query('searchTerm', ''));
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $response = $this->peopleService->getPersonById($request->query('id',''));
        return response()->json($response);
    }
}
