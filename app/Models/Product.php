<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'slug', 'description', 'price', 'image_url', 'size', 'status', 'category_id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'subtotal');
    }
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
    public function productSizes()
    {
        return $this->hasMany(ProductSize::class);
    }
}
