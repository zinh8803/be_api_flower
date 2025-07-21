<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReport extends Model
{
    protected $fillable = [
        'order_id',
        'order_detail_id',
        'user_id',
        'reason',
        'image_url',
        'status',
        'quantity'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
