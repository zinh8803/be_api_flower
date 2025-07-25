<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['name', 'order_code', 'email', 'phone', 'address', 'note', 'total_price', 'status', 'user_id', 'buy_at', 'payment_method', 'discount_id', 'discount_amount', 'delivery_date', 'delivery_time', 'is_express', 'status_stock'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'subtotal');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
    public function productReports()
    {
        return $this->hasMany(ProductReport::class);
    }
    public function orderReturns()
    {
        return $this->hasMany(OrderReturn::class);
    }
    // public function getTotalPriceAttribute($value)
    // {
    //     return number_format($value, 0, ',', '.');
    // }
}
