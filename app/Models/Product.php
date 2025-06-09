<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'image', 'size','status', 'category_id'];

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
}
