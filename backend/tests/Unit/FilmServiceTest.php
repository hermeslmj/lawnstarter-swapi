<?php

namespace Tests\Unit;

use App\Services\FilmService;
use App\Services\CacheService;
use App\Dtos\FilmDTO;
use App\Dtos\ListDTO;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class FilmServiceTest extends TestCase
{
    private FilmService $filmService;
    private $mockCacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockCacheService = Mockery::mock(CacheService::class);
        $this->filmService = new FilmService($this->mockCacheService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_films_by_search_returns_success_response()
    {
        $this->mockCacheService->shouldReceive('get')->andReturn(null);
        $this->mockCacheService->shouldReceive('set')->andReturn(true);

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

    public function test_get_films_by_search_returns_from_cache()
    {
        $cachedFilm = new ListDTO('1', 'A New Hope');
        $this->mockCacheService
            ->shouldReceive('get')
            ->with('list_film_id_1')
            ->andReturn($cachedFilm);

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

    public function test_get_film_by_id_returns_from_cache()
    {
        $cachedFilm = new FilmDTO('1', 'Cached Film', 'Opening crawl', []);
        $this->mockCacheService
            ->shouldReceive('get')
            ->with('film_id_1')
            ->andReturn($cachedFilm);

        $response = $this->filmService->getFilmById('1');

        $this->assertTrue($response->success);
        $this->assertInstanceOf(FilmDTO::class, $response->data);
        $this->assertEquals('Cached Film', $response->data->title);
    }

    public function test_get_film_by_id_returns_from_api()
    {
        $this->mockCacheService->shouldReceive('get')->andReturn(null);
        $this->mockCacheService->shouldReceive('set')->once()->andReturn(true);

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
        $this->mockCacheService->shouldReceive('get')->andReturn(null);

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