<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use App\Dtos\FilmDTO;

class FilmService
{
    public function getFilmsBySearch(string $searchTerm): array
    {
        #TODO: use env var to swapi url
       $response = Http::get('https://www.swapi.tech/api/films/', [ 
           'title' => $searchTerm
       ]);

       $filmsListObj = json_decode($response->body(), true);

       $filmDTOArray = array_map(function ($film) {
            $characterData = $this->_getPersonDataForFilm($film['properties']['characters'] ?? []);
           return new FilmDTO(
               $film['uid'] ?? '',
               $film['properties']['title'] ?? '',
               $film['properties']['opening_crawl'] ?? '',
               $characterData ?? []
           );
       }, $filmsListObj['result'] ?? []);

       return $filmDTOArray;
    }

    public function getFilmById(string $id)
    {
        $response = Http::get("https://www.swapi.tech/api/films/{$id}");
        $filmObj = json_decode($response->body(), true);
        $characterData = $this->_getPersonDataForFilm($filmObj['result']['properties']['characters'] ?? []);
        return new FilmDTO(
            $filmObj['result']['uid'] ?? '',
            $filmObj['result']['properties']['title'] ?? '',
            $filmObj['result']['properties']['opening_crawl'] ?? '',
            $characterData ?? []
        );
    }

    private function _getPersonDataForFilm(array $personUrls): array
    {
        $people = [];
        foreach ($personUrls as $url) {
            $response = Http::get($url);
            $personObj = json_decode($response->body(), true);
            $people[] = [
                'uid' => $personObj['result']['uid'] ?? '',
                'name' => $personObj['result']['properties']['name'] ?? ''
            ];
        }
        return $people;
    }
}

