<?php

namespace App\Services;

use App\Dtos\ListDTO;
use Illuminate\Support\Facades\Http;
use App\Dtos\PeopleDTO;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Http\Response\ServiceResponse;

class PeopleService
{
    protected $swapiBaseUrl;
    protected $swapiPeopleUrl;
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->swapiBaseUrl = env('APP_SWAPI_URL');
        $this->swapiPeopleUrl = "{$this->swapiBaseUrl}people/";
        $this->cacheService = $cacheService;
    }

    public function getPeopleBySearch(string $searchTerm): ServiceResponse
    {
        try {
            $response = Http::get($this->swapiPeopleUrl, [
                'name' => $searchTerm
            ]);

            if (!$response || !$response->successful()) {
                Log::error('Failed to fetch people from SWAPI', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return new ServiceResponse([], 'Failed to fetch people', false);
            }

            $peopleListObj = json_decode($response->body(), true);

            $listPeopleDTOArray = array_map(function ($person) {
                $cacheKey = $person['uid'] ? 'list_person_id_' . $person['uid'] : null;

                if ($cacheKey) {
                    $cachedPerson = $this->cacheService->get($cacheKey);
                    if ($cachedPerson) {
                        return $cachedPerson;
                    }
                }
                
                $peopleListDto = new ListDTO(
                    $person['uid'] ?? '',
                    $person['properties']['name'] ?? '',
                );

                if ($cacheKey) {
                    $this->cacheService->set($cacheKey, $peopleListDto);
                }

                return $peopleListDto;
            }, $peopleListObj['result'] ?? []);

            return new ServiceResponse($listPeopleDTOArray, 'People retrieved successfully', true);
        } catch (Exception $e) {
            Log::error('Exception in getPeopleBySearch', ['message' => $e->getMessage()]);
            return new ServiceResponse([], 'An error occurred while fetching people', false, 500);
        }
    }

    public function getPersonById(string $id): ServiceResponse
    {
        $cacheKey = 'person_id_' . $id;
        $personDTO = $this->cacheService->get($cacheKey);
        
        if ($personDTO !== null) {
            return new ServiceResponse($personDTO, 'Person retrieved from cache', true);
        }

        try {
            $response = Http::get("{$this->swapiPeopleUrl}{$id}"); 
            if (!$response->successful()) {
                Log::error('Failed to fetch person by id from SWAPI', [
                    'id' => $id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return new ServiceResponse(null, 'Person not found', false, 404);
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

            $this->cacheService->set($cacheKey, $personDTO);

            return new ServiceResponse($personDTO, 'Person retrieved successfully', true);
        } catch (Exception $e) {
            Log::error('Exception in getPersonById', ['id' => $id, 'message' => $e->getMessage()]);
            return new ServiceResponse(null, 'An error occurred while fetching the person', false, 500);
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
                if (!$response || $response->successful()) {
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
