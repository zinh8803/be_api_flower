<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = ['order_id', 'quantity', 'product_size_id', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }
    public function productReports()
    {
        return $this->hasMany(ProductReport::class, 'order_detail_id');
    }
    public function orderReturns()
    {
        return $this->hasMany(OrderReturn::class);
    }
}
