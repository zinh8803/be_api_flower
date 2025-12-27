<?php

namespace App\Repositories\Eloquent;

use App\Models\Color;
use App\Repositories\Contracts\ColorRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ColorRepository implements ColorRepositoryInterface
{
    // public function getAll()
    // {
    //     $key = 'colors';
    //     $start = microtime(true);
    //     $jsonFromRedis = null;
    //     try {
    //         $redis = Redis::connection();
    //         $jsonFromRedis = $redis->get($key);
    //     } catch (\Exception $e) {
    //         Log::error('Redis connection error: ' . $e->getMessage());
    //     }
    //     $redisTime = microtime(true);
    //     if ($jsonFromRedis) {
    //         $colorsArray = json_decode($jsonFromRedis, true);
    //         $colors = Color::hydrate($colorsArray);
    //     } else {
    //         $colors = Color::all();
    //         $jsonColors = $colors->toJson();
    //         try {
    //             if (isset($redis)) {
    //                 $redis->set($key, $jsonColors);
    //                 $redis->expire($key, 3600);
    //             }
    //         } catch (\Exception $e) {
    //             Log::error('Redis set error: ' . $e->getMessage());
    //         }
    //     }
    //     $end = microtime(true);
    //     Log::info('Redis get: ' . round(($redisTime - $start) * 1000, 2) . 'ms, Hydrate/DB: ' . round(($end - $redisTime) * 1000, 2) . 'ms, Total: ' . round(($end - $start) * 1000, 2) . 'ms');
    //     return $colors;
    // }
    public function getAll()
    {
        $key = 'colors';

        $cached = Redis::get($key);
        if ($cached) {
            return json_decode($cached, true);
        }

        $colors = Color::select('id', 'name')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ])
            ->toArray();

        Redis::setex($key, 3600, json_encode($colors));

        return $colors;
    }


    public function findById($id)
    {
        return Color::findOrFail($id);
    }

    public function create(array $data)
    {
        return Color::create($data);
    }

    public function update($id, array $data)
    {
        $color = Color::findOrFail($id);
        $color->update($data);
        return $color;
    }

    public function delete($id)
    {
        $color = Color::findOrFail($id);
        $color->delete();
        return $color;
    }
}
