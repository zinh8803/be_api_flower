<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flower extends Model
{
    protected $fillable = ['name', 'color', 'price', 'flower_type_id'];
    public function flowerType()
    {
        return $this->belongsTo(FlowerType::class);
    }
}
