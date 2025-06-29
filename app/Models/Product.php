<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'price', 'image_url', 'size','status', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity','subtotal');
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
