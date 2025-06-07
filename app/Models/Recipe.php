<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['name', 'description', 'ingredients', 'instructions', 'image_url'];

  

    public function product()
{
    return $this->belongsTo(Product::class);
}

public function flower()
{
    return $this->belongsTo(Flower::class);
}
}
