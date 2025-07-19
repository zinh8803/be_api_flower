<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['quantity', 'flower_id'];


    public function flower()
    {
        return $this->belongsTo(Flower::class);
    }
    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }
}
