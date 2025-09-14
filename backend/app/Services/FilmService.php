<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Dtos\FilmDTO;
use App\DTOs\PeopleDTO;
use App\Dtos\ListDTO;

class FilmService
{
    protected $swapiBaseUrl;
    protected $swapiFilmsUrl;

    public function __construct()
    {
        $this->swapiBaseUrl = env('APP_SWAPI_URL');
        $this->swapiFilmsUrl = "{$this->swapiBaseUrl}films/";
    }

    public function getFilmsBySearch(string $searchTerm): array
    {
        try {
            $response = Http::get($this->swapiFilmsUrl, [
                'title' => $searchTerm
            ]);

            if (!$response->successful()) {
                Log::error('Failed to fetch films from SWAPI', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            }

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
        } catch (Exception $e) {
            Log::error('Exception in getFilmsBySearch', ['message' => $e->getMessage()]);
            return [];
        }
    }

    public function getFilmById(string $id)
    {
        $cacheKey = 'film_id_' . $id;
        $filmDTO = Cache::get($cacheKey);
        if ($filmDTO !== null) {
            return $filmDTO;
        }
        try {
            $response = Http::get("{$this->swapiFilmsUrl}{$id}");
            if (!$response->successful()) {
                Log::error('Failed to fetch film by id from SWAPI', ['id' => $id, 'status' => $response->status(), 'body' => $response->body()]);
                return null;
            }
            $filmObj = json_decode($response->body(), true);
            $characterData = $this->_getPersonDataForFilm($filmObj['result']['properties']['characters'] ?? []);
            $filmDTO = new FilmDTO(
                $filmObj['result']['uid'] ?? '',
                $filmObj['result']['properties']['title'] ?? '',
                $filmObj['result']['properties']['opening_crawl'] ?? '',
                $characterData ?? []
            );
            Cache::put($cacheKey, $filmDTO, now()->addMinutes(10));
            return $filmDTO;
        } catch (Exception $e) {
            Log::error('Exception in getFilmById', ['id' => $id, 'message' => $e->getMessage()]);
            return null;
        }
    }

    private function _getPersonDataForFilm(array $personUrls): array
    {
        $people = [];
        try {
            $response = Http::pool(function ($pool) use ($personUrls) {
                $requests = [];
                foreach ($personUrls as $url) {
                    $requests[] = $pool->get($url);
                }
                return $requests;
            });

            foreach ($response as $res) {
                if ($res->successful()) {
                    $personObj = json_decode($res->body(), true);
                    $people[] = [
                        'uid' => $personObj['result']['uid'] ?? '',
                        'name' => $personObj['result']['properties']['name'] ?? ''
                    ];
                } else {
                    Log::warning('Failed to fetch person for film', ['status' => $res->status(), 'body' => $res->body()]);
                }
            }
        } catch (Exception $e) {
            Log::error('Exception in _getPersonDataForFilm', ['message' => $e->getMessage()]);
        }
        return $people;
    }
}
