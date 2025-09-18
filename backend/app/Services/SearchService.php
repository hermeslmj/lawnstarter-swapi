<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Http\Response\ServiceResponse;
use App\Dtos\FilmDTO;
use App\Dtos\PeopleDTO;
use App\Dtos\ListDTO;

class SearchService
{
    protected $swapiBaseUrl;
    protected $cacheService;

    //can be a data base config
    protected $entityConfig = [
        'films' => [
            'endpoint' => 'films/',
            'query_param' => 'title',
        ],
        'people' => [
            'endpoint' => 'people/',
            'query_param' => 'name',
        ]
    ];

    public function __construct(CacheService $cacheService)
    {
        $this->swapiBaseUrl = env('APP_SWAPI_URL');
        $this->cacheService = $cacheService;
    }

    /**
     * Generic search method for any entity.
     *
     * @param string $entityType
     * @param string $searchTerm
     * @return ServiceResponse
     */
    public function search(string $entityType, string $searchTerm): ServiceResponse
    {
        if (!isset($this->entityConfig[$entityType])) {
            return new ServiceResponse([], 'Entity type not supported', false, 400);
        }

        $endpoint = $this->entityConfig[$entityType]['endpoint'];
        $queryParam = $this->entityConfig[$entityType]['query_param'];
        $url = "{$this->swapiBaseUrl}{$endpoint}";
        $cacheKey = "search_{$entityType}_" . md5($searchTerm);

        // Try to get from cache
        try {
            $cached = $this->cacheService->get($cacheKey);
            if ($cached !== null) {
                return new ServiceResponse($cached, ucfirst($entityType) . ' retrieved from cache', true);
            }
        } catch (Exception $e) {
            Log::warning('Cache unavailable for search', ['error' => $e->getMessage()]);
        }

        try {
            $response = Http::get($url, [
                $queryParam => $searchTerm
            ]);

            if (!$response || !$response->successful()) {
                Log::error("Failed to fetch {$entityType} from SWAPI", [
                    'status' => $response ? $response->status() : null,
                    'body' => $response ? $response->body() : null
                ]);
                return new ServiceResponse([], "Failed to fetch {$entityType}", false);
            }

            $resultObj = json_decode($response->body(), true);
            $results = $resultObj['result'] ?? [];

            // DTO mapping
            $mappedResults = [];
            if ($entityType === 'films') {
                foreach ($results as $item) {
                    $mappedResults[] = new ListDTO(
                        $item['uid'] ?? '',
                        $item['properties']['title'] ?? ''
                    );
                }
            } elseif ($entityType === 'people') {
                foreach ($results as $item) {
                    $mappedResults[] = new ListDTO(
                        $item['uid'] ?? '',
                        $item['properties']['name'] ?? ''
                    );
                }
            } else {
                $mappedResults = $results;
            }

            // Try to set cache
            try {
                $this->cacheService->set($cacheKey, $mappedResults);
            } catch (Exception $e) {
                Log::warning('Cache unavailable for search set', ['error' => $e->getMessage()]);
            }

            return new ServiceResponse($mappedResults, ucfirst($entityType) . ' retrieved successfully', true);
        } catch (Exception $e) {
            Log::error("Exception in search for {$entityType}", ['message' => $e->getMessage()]);
            return new ServiceResponse([], 'An error occurred while fetching data', false, 500);
        }
    }

    /**
     * Generic search by ID method for any entity.
     *
     * @param string $entityType
     * @param string $id
     * @return ServiceResponse
     */
    public function searchById(string $entityType, string $id): ServiceResponse
    {
        if (!isset($this->entityConfig[$entityType])) {
            return new ServiceResponse(null, 'Entity type not supported', false, 400);
        }

        $endpoint = $this->entityConfig[$entityType]['endpoint'];
        $url = "{$this->swapiBaseUrl}{$endpoint}{$id}";
        $cacheKey = "searchById_{$entityType}_{$id}";

        // Try to get from cache
        try {
            $cached = $this->cacheService->get($cacheKey);
            if ($cached !== null) {
                return new ServiceResponse($cached, ucfirst($entityType) . ' retrieved from cache', true);
            }
        } catch (Exception $e) {
            Log::warning('Cache unavailable for searchById', ['error' => $e->getMessage()]);
        }

        try {
            $response = Http::get($url);

            if (!$response || !$response->successful()) {
                Log::error("Failed to fetch {$entityType} by id from SWAPI", [
                    'status' => $response ? $response->status() : null,
                    'body' => $response ? $response->body() : null
                ]);
                return new ServiceResponse(null, ucfirst($entityType) . ' not found', false, 404);
            }

            $resultObj = json_decode($response->body(), true);
            $item = $resultObj['result'] ?? null;

            // DTO mapping with related data
            $mappedItem = null;
            if ($entityType === 'films' && $item) {
                $characters = $this->fetchRelatedData($item['properties']['characters'] ?? [], 'people');
                $mappedItem = new FilmDTO(
                    $item['uid'] ?? '',
                    $item['properties']['title'] ?? '',
                    $item['properties']['opening_crawl'] ?? '',
                    $characters
                );
            } elseif ($entityType === 'people' && $item) {
                $movies = $this->fetchRelatedData($item['properties']['films'] ?? [], 'films');
                $mappedItem = new PeopleDTO(
                    $item['uid'] ?? '',
                    $item['properties']['name'] ?? '',
                    $item['properties']['gender'] ?? '',
                    $item['properties']['eye_color'] ?? '',
                    $item['properties']['hair_color'] ?? '',
                    $item['properties']['height'] ?? '',
                    $item['properties']['mass'] ?? '',
                    $item['properties']['birth_year'] ?? '',
                    $movies
                );
            } else {
                $mappedItem = $item;
            }

            // Try to set cache
            try {
                $this->cacheService->set($cacheKey, $mappedItem);
            } catch (Exception $e) {
                Log::warning('Cache unavailable for searchById set', ['error' => $e->getMessage()]);
            }

            return new ServiceResponse($mappedItem, ucfirst($entityType) . ' retrieved successfully', true);
        } catch (Exception $e) {
            Log::error("Exception in searchById for {$entityType}", ['message' => $e->getMessage()]);
            return new ServiceResponse(null, 'An error occurred while fetching data', false, 500);
        }
    }

    /**
     * Fetch related data for a list of URLs (e.g., films for people, characters for films).
     *
     * @param array $urls
     * @param string $relatedType
     * @return array
     */
    private function fetchRelatedData(array $urls, string $relatedType): array
    {
        $relatedData = [];
        foreach ($urls as $url) {
            try {
                $response = Http::get($url);
                if ($response && $response->successful()) {
                    $resultObj = json_decode($response->body(), true);
                    $item = $resultObj['result'] ?? null;
                    if ($relatedType === 'films' && $item) {
                        $relatedData[] = [
                            'uid' => $item['uid'] ?? '',
                            'title' => $item['properties']['title'] ?? '',
                        ];
                    } elseif ($relatedType === 'people' && $item) {
                        $relatedData[] = [
                            'uid' => $item['uid'] ?? '',
                            'name' => $item['properties']['name'] ?? '',
                        ];
                    }
                }
            } catch (Exception $e) {
                Log::warning("Failed to fetch related {$relatedType} data", ['url' => $url, 'error' => $e->getMessage()]);
            }
        }
        return $relatedData;
    }
}
