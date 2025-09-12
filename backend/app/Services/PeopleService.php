<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Dtos\PeopleDTO;
use Illuminate\Support\Facades\Cache;


class PeopleService
{
    public function getPeopleBySearch(string $searchTerm): array
    {
        #TODO: use env var to swapi url
        $response = Http::get('https://www.swapi.tech/api/people/', [
            'name' => $searchTerm
        ]);

        $peopleListObj = json_decode($response->body(), true);

        $peopleDTOArray = array_map(function ($person) {
            $cacheKey = $person['uid'] ? 'person_id_' . $person['uid'] : null;

            if ($cacheKey && ($cachedPerson = Cache::get($cacheKey))) {
                return $cachedPerson;
            }

            $movieData = $this->_getMoviesDataForPerson($person['properties']['films'] ?? []);
            $personDTO = new PeopleDTO(
                $person['uid'] ?? '',
                $person['properties']['name'] ?? '',
                $person['properties']['gender'] ?? '',
                $person['properties']['eye_color'] ?? '',
                $person['properties']['hair_color'] ?? '',
                $person['properties']['height'] ?? '',
                $person['properties']['mass'] ?? '',
                $person['properties']['birth_year'] ?? '',
                $movieData
            );

            if ($cacheKey) {
                Cache::put($cacheKey, $personDTO, now()->addMinutes(10));
            }

            return $personDTO;
        }, $peopleListObj['result'] ?? []);

        return $peopleDTOArray;
    }

    public function getPersonById(string $id)
    {
        $cacheKey = 'person_id_' . $id;
        $personDTO = Cache::get($cacheKey);
        if ($personDTO !== null) {
            return $personDTO;
        }

        $response = Http::get("https://www.swapi.tech/api/people/{$id}");
        $personObj = json_decode($response->body(), true);
        $movieData = $this->_getMoviesDataForPerson($personObj['result']['properties']['films'] ?? []);
        $personDTO = new PeopleDTO(
            $personObj['result']['uid'] ?? '',
            $personObj['result']['properties']['name'] ?? '',
            $personObj['result']['properties']['gender'] ?? '',
            $personObj['result']['properties']['eye_color'] ?? '',
            $personObj['result']['properties']['hair_color'] ?? '',
            $personObj['result']['properties']['height'] ?? '',
            $personObj['result']['properties']['mass'] ?? '',
            $personObj['result']['properties']['birth_year'] ?? '',
            $movieData
        );

        // Store in cache for 10 minutes
        Cache::put($cacheKey, $personDTO, now()->addMinutes(10));

        return $personDTO;
    }

    private function _getMoviesDataForPerson(array $movieUrls): array
    {
        $movies = [];
        
        $responses = Http::pool(function ($pool) use ($movieUrls) {
            $requests = [];
            foreach ($movieUrls as $url) {
                $requests[] = $pool->get($url);
            }
            return $requests;
        });

        foreach ($responses as $response) {
            if ($response->successful()) {
                $movieData = $response->json()['result'] ?? null;
                if ($movieData) {
                    $movies[] = [
                        'uid' => $movieData['uid'],
                        'title' => $movieData['properties']['title'] ?? ''
                    ];
                }
            }
        }

        return $movies;
    }
}
