<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity','product_size_id', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
    public function productSize()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }
}
