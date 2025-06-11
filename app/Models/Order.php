<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['name','email','phone','address','note', 'total_price','status','user_id','buy_at','payment_method','discount_id','user_id', 'discount_amount'];

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
    public function getTotalPriceAttribute($value)
    {
        return number_format($value, 0, ',', '.');
    }
}
