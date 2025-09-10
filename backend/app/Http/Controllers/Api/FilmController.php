<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FilmService;

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
        $response = $this->filmService->getFilmsBySearch($request->query('searchTerm', ''));
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $response = $this->filmService->getFilmById($request->query('id',''));
        return response()->json($response);
    }
   
        
}
