<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'product_size_id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_size_id');
    }
}
