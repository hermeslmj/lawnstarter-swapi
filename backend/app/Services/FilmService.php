<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class FilmService
{
    public function getFilmsBySearch(string $searchTerm): array
    {
        #TODO: use env var to swapi url
       $response = Http::get('https://www.swapi.tech/api/films/', [ 
           'title' => $searchTerm
       ]);

       return $response->json()['result'] ?? [];
    }

    public function getFilmById(int $id)
    {
        $response = Http::get("https://www.swapi.tech/api/films/{$id}");

        return $response->json()['result'] ?? null;
    }
}

