<?php

namespace App\Repositories\Eloquent;

use App\Models\Color;
use App\Repositories\Contracts\ColorRepositoryInterface;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ColorRepository extends BaseRepository implements ColorRepositoryInterface
{
    public function __construct(Color $color)
    {
        parent::__construct($color);
    }

    public function getAll()
    {
        $key = 'colors';

        try {
            $cached = Redis::get($key);
            if ($cached) {
                return json_decode($cached, true);
            }
        } catch (\Throwable $e) {
            Log::warning('Redis get colors failed', ['error' => $e->getMessage()]);
        }

        $colors = Color::select('id', 'name')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ])
            ->toArray();

        try {
            Redis::setex($key, 3600, json_encode($colors));
        } catch (\Throwable $e) {
            Log::warning('Redis setex colors failed', ['error' => $e->getMessage()]);
        }

        return $colors;
    }
}
