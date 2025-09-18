<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PeopleController;
use App\Http\Controllers\Api\FilmController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\SearchController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('people', PeopleController::class);
Route::apiResource('films', FilmController::class);
Route::apiResource('statistics', StatisticsController::class);
Route::apiResource('search', SearchController::class);
