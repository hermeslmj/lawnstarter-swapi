<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

use App\Dtos\FilmDTO;
use App\DTOs\PeopleDTO;
use App\Dtos\ListDTO;

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
            $cacheKey = $film['uid'] ? 'list_film_id_' . $film['uid'] : null;
            if ($cacheKey && ($cachedFilm = Cache::get($cacheKey))) {
                return $cachedFilm;
            }
            $listFilmDto = new ListDTO(
                $film['uid'] ?? '',
                $film['properties']['title'] ?? '',
            );
            if ($cacheKey) {
                Cache::put($cacheKey, $listFilmDto, now()->addMinutes(10));
            }
            return $listFilmDto;
        }, $filmsListObj['result'] ?? []);

        return $filmDTOArray;
    }

    public function getFilmById(string $id)
    {
        $cacheKey = 'film_id_' . $id;
        $filmDTO = Cache::get($cacheKey);
        if ($filmDTO !== null) {
            return $filmDTO;
        }
        $response = Http::get("https://www.swapi.tech/api/films/{$id}");
        $filmObj = json_decode($response->body(), true);
        $characterData = $this->_getPersonDataForFilm($filmObj['result']['properties']['characters'] ?? []);
        $filmDTO = new FilmDTO(
            $filmObj['result']['uid'] ?? '',
            $filmObj['result']['properties']['title'] ?? '',
            $filmObj['result']['properties']['opening_crawl'] ?? '',
            $characterData ?? []
        );

        // Store in cache for 10 minutes
        Cache::put($cacheKey, $filmDTO, now()->addMinutes(10));

        return $filmDTO;
    }

    private function _getPersonDataForFilm(array $personUrls): array
    {
        $people = [];
        $response = Http::pool(function ($pool) use ($personUrls) {
            $requests = [];
            foreach ($personUrls as $url) {
                $requests[] = $pool->get($url);
            }
            return $requests;
        });

        foreach ($response as $res) {
            $personObj = json_decode($res->body(), true);
            $people[] = [
                'uid' => $personObj['result']['uid'] ?? '',
                'name' => $personObj['result']['properties']['name'] ?? ''
            ];
        }

        return $people;
    }
}
