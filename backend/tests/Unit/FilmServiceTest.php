<?php

namespace Tests\Unit;

use App\Services\FilmService;
use App\Dtos\FilmDTO;
use App\Dtos\ListDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FilmServiceTest extends TestCase
{
    private FilmService $filmService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filmService = new FilmService();
    }

    public function test_get_films_by_search_returns_success_response()
    {
        Http::fake([
            '*' => Http::response([
                'result' => [
                    [
                        'uid' => '1',
                        'properties' => [
                            'title' => 'A New Hope'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->filmService->getFilmsBySearch('hope');

        $this->assertTrue($response->success);
        $this->assertIsArray($response->data);
        $this->assertInstanceOf(ListDTO::class, $response->data[0]);
        $this->assertEquals('A New Hope', $response->data[0]->title);
    }

    public function test_get_films_by_search_returns_error_on_api_failure()
    {
        Http::fake([
            '*' => Http::response(null, 500)
        ]);

        $response = $this->filmService->getFilmsBySearch('hope');

        $this->assertFalse($response->success);
        $this->assertEquals([], $response->data);
    }

    public function test_get_film_by_id_returns_from_cache()
    {
        $cachedFilm = new FilmDTO('1', 'Cached Film', 'Opening crawl', []);
        Cache::shouldReceive('get')
            ->once()
            ->with('film_id_1')
            ->andReturn($cachedFilm);

        $response = $this->filmService->getFilmById('1');

        $this->assertTrue($response->success);
        $this->assertInstanceOf(FilmDTO::class, $response->data);
        $this->assertEquals('Cached Film', $response->data->title);
    }

    public function test_get_film_by_id_returns_from_api()
    {
        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->once();

        Http::fake([
            '*' => Http::response([
                'result' => [
                    'uid' => '1',
                    'properties' => [
                        'title' => 'A New Hope',
                        'opening_crawl' => 'It is a period of civil war...',
                        'characters' => []
                    ]
                ]
            ], 200)
        ]);

        $response = $this->filmService->getFilmById('1');

        $this->assertTrue($response->success);
        $this->assertInstanceOf(FilmDTO::class, $response->data);
        $this->assertEquals('A New Hope', $response->data->title);
    }

    public function test_get_film_by_id_returns_error_on_api_failure()
    {
        Cache::shouldReceive('get')->andReturn(null);
        Http::fake([
            '*' => Http::response(null, 404)
        ]);

        $response = $this->filmService->getFilmById('999');

        $this->assertFalse($response->success);
        $this->assertEquals(404, $response->code);
        $this->assertNull($response->data);
    }

    public function test_get_person_data_for_film()
    {
        Http::fake([
            '*' => Http::response([
                'result' => [
                    'uid' => '1',
                    'properties' => [
                        'name' => 'Luke Skywalker'
                    ]
                ]
            ], 200)
        ]);

        $method = new \ReflectionMethod(FilmService::class, '_getPersonDataForFilm');
        $method->setAccessible(true);

        $result = $method->invoke($this->filmService, ['https://swapi.dev/api/films/1']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Luke Skywalker', $result[0]['name']);
    }
}