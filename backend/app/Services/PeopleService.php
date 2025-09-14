<?php

namespace App\Services;

use App\Dtos\ListDTO;
use Illuminate\Support\Facades\Http;
use App\Dtos\PeopleDTO;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class PeopleService
{
    protected $swapiBaseUrl;
    protected $swapiPeopleUrl;
    public function __construct()
    {
        $this->swapiBaseUrl = env('APP_SWAPI_URL');
        $this->swapiPeopleUrl = "{$this->swapiBaseUrl}people/";
    }

    public function getPeopleBySearch(string $searchTerm): array
    {
        try {
            $response = Http::get($this->swapiPeopleUrl, [
                'name' => $searchTerm
            ]);

            if (!$response->successful()) {
                Log::error('Failed to fetch people from SWAPI', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

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
        } catch (Exception $e) {
            Log::error('Exception in getPeopleBySearch', ['message' => $e->getMessage()]);
            return [];
        }
    }

    public function getPersonById(string $id)
    {
        $cacheKey = 'person_id_' . $id;
        $personDTO = Cache::get($cacheKey);
        if ($personDTO !== null) {
            return $personDTO;
        }

        try {
            $response = Http::get("{$this->swapiPeopleUrl}{$id}"); 
            if (!$response->successful()) {
                Log::error('Failed to fetch person by id from SWAPI', [
                    'id' => $id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
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

            Cache::put($cacheKey, $personDTO, now()->addMinutes(10));

            return $personDTO;
        } catch (Exception $e) {
            Log::error('Exception in getPersonById', ['id' => $id, 'message' => $e->getMessage()]);
            return null;
        }
    }

    private function _getMoviesDataForPerson(array $movieUrls): array
    {
        $movies = [];
        try {
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
                } else {
                    Log::warning('Failed to fetch movie for person', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('Exception in _getMoviesDataForPerson', ['message' => $e->getMessage()]);
        }

        return $movies;
    }
}
