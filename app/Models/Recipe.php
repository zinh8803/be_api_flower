<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['quantity', 'product_id', 'flower_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function flower()
    {
        return $this->belongsTo(Flower::class);
    }
    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }
}
