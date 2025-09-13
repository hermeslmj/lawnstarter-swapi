<?php

namespace App\Services;

use App\Dtos\ListDTO;
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

        $listPeopleDTOArray = array_map(function ($person) {
            $cacheKey = $person['uid'] ? 'list_person_id_' . $person['uid'] : null;

            if ($cacheKey && ($cachedPerson = Cache::get($cacheKey))) {
                return $cachedPerson;
            }
            
            $peopleListDto = new ListDTO(
                $person['uid'] ?? '',
                $person['properties']['name'] ?? '',
            );

            if ($cacheKey) {
                Cache::put($cacheKey, $peopleListDto, now()->addMinutes(10));
            }

            return $peopleListDto;
        }, $peopleListObj['result'] ?? []);
        return $listPeopleDTOArray;
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
