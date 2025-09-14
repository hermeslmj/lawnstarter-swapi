<?php


namespace Tests\Unit;

use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Exception;
use Mockery;

class CacheServiceTest extends TestCase
{
    private CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new CacheService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_returns_cached_value()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('test-key')
            ->andReturn('cached-value');

        $result = $this->cacheService->get('test-key');
        
        $this->assertEquals('cached-value', $result);
    }

    public function test_get_handles_exception()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('test-key')
            ->andThrow(new Exception('Connection refused'));

        Log::shouldReceive('warning')
            ->once()
            ->with('Redis is unavailable', Mockery::hasKey('error'));

        $result = $this->cacheService->get('test-key');
        
        $this->assertNull($result);
    }

    public function test_set_stores_value_in_cache()
    {
        Cache::shouldReceive('put')
            ->once()
            ->with('test-key', 'test-value', Mockery::any())
            ->andReturn(true);

        $result = $this->cacheService->set('test-key', 'test-value');
        
        $this->assertTrue($result);
    }

    public function test_set_handles_exception()
    {
        Cache::shouldReceive('put')
            ->once()
            ->andThrow(new Exception('Connection refused'));

        Log::shouldReceive('warning')
            ->once()
            ->with('Redis is unavailable', Mockery::hasKey('error'));

        $result = $this->cacheService->set('test-key', 'test-value');
        
        $this->assertFalse($result);
    }
}