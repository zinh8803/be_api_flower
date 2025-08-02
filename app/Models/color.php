<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = ['name', 'hex_code'];

    public function flowers()
    {
        return $this->hasMany(Flower::class);
    }

    public function getRouteKeyName()
    {
        return 'name';
    }
}
