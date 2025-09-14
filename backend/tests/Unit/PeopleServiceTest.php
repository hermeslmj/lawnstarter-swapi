<?php

namespace Tests\Unit;

use App\Services\PeopleService;
use App\Services\CacheService;
use App\Dtos\PeopleDTO;
use App\Dtos\ListDTO;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class PeopleServiceTest extends TestCase
{
    private PeopleService $peopleService;
    private $mockCacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockCacheService = Mockery::mock(CacheService::class);
        $this->peopleService = new PeopleService($this->mockCacheService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_people_by_search_returns_success_response()
    {
        $this->mockCacheService->shouldReceive('get')->andReturn(null);
        $this->mockCacheService->shouldReceive('set')->andReturn(true);

        Http::fake([
            '*' => Http::response([
                'result' => [
                    [
                        'uid' => '1',
                        'properties' => [
                            'name' => 'Luke Skywalker'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->peopleService->getPeopleBySearch('luke');

        $this->assertTrue($response->success);
        $this->assertIsArray($response->data);
        $this->assertInstanceOf(ListDTO::class, $response->data[0]);
        $this->assertEquals('Luke Skywalker', $response->data[0]->title);
    }

    public function test_get_people_by_search_returns_from_cache()
    {
        $cachedPerson = new ListDTO('1', 'Luke Skywalker');
        $this->mockCacheService
            ->shouldReceive('get')
            ->with('list_person_id_1')
            ->andReturn($cachedPerson);

        Http::fake([
            '*' => Http::response([
                'result' => [
                    [
                        'uid' => '1',
                        'properties' => [
                            'name' => 'Luke Skywalker'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->peopleService->getPeopleBySearch('luke');

        $this->assertTrue($response->success);
        $this->assertIsArray($response->data);
        $this->assertInstanceOf(ListDTO::class, $response->data[0]);
        $this->assertEquals('Luke Skywalker', $response->data[0]->title);
    }

    public function test_get_person_by_id_returns_from_cache()
    {
        $cachedPerson = new PeopleDTO('1', 'Luke Skywalker', 'male', 'blue', 'blond', '172', '77', '19BBY', []);
        $this->mockCacheService
            ->shouldReceive('get')
            ->with('person_id_1')
            ->andReturn($cachedPerson);

        $response = $this->peopleService->getPersonById('1');

        $this->assertTrue($response->success);
        $this->assertInstanceOf(PeopleDTO::class, $response->data);
        $this->assertEquals('Luke Skywalker', $response->data->name);
    }

    public function test_get_person_by_id_returns_from_api()
    {
        $this->mockCacheService->shouldReceive('get')->andReturn(null);
        $this->mockCacheService->shouldReceive('set')->once()->andReturn(true);

        Http::fake([
            '*' => Http::response([
                'result' => [
                    'uid' => '1',
                    'properties' => [
                        'name' => 'Luke Skywalker',
                        'gender' => 'male',
                        'eye_color' => 'blue',
                        'hair_color' => 'blond',
                        'height' => '172',
                        'mass' => '77',
                        'birth_year' => '19BBY',
                        'films' => []
                    ]
                ]
            ], 200)
        ]);

        $response = $this->peopleService->getPersonById('1');

        $this->assertTrue($response->success);
        $this->assertInstanceOf(PeopleDTO::class, $response->data);
        $this->assertEquals('Luke Skywalker', $response->data->name);
    }

    public function test_get_person_by_id_returns_error_on_api_failure()
    {
        $this->mockCacheService->shouldReceive('get')->andReturn(null);

        Http::fake([
            '*' => Http::response(null, 404)
        ]);

        $response = $this->peopleService->getPersonById('999');

        $this->assertFalse($response->success);
        $this->assertEquals(404, $response->code);
        $this->assertNull($response->data);
    }

    public function test_get_movies_data_for_person()
    {
        Http::fake([
            '*' => Http::response([
                'result' => [
                    'uid' => '1',
                    'properties' => [
                        'title' => 'A New Hope'
                    ]
                ]
            ], 200)
        ]);

        $method = new \ReflectionMethod(PeopleService::class, '_getMoviesDataForPerson');
        $method->setAccessible(true);

        $result = $method->invoke($this->peopleService, ['https://swapi.dev/api/films/1']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('A New Hope', $result[0]['title']);
    }
}
