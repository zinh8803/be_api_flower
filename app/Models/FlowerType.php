<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowerType extends Model
{
    protected $fillable = ['id', 'name'];

    public function flowers()
    {
        return $this->hasMany(Flower::class);
    }
}
