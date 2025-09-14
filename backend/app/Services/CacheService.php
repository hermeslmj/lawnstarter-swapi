<?php


namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class CacheService
{
    public function get(string $key)
    {
        try {
            return Cache::get($key);
        } catch (Exception $e) {
            Log::warning('Redis is unavailable', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function set(string $key, $value, $ttl = null): bool
    {
        try {
            Cache::put($key, $value, $ttl ?? now()->addMinutes(10));
            return true;
        } catch (Exception $e) {
            Log::warning('Redis is unavailable', ['error' => $e->getMessage()]);
            return false;
        }
    }
}