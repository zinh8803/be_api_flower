<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = ['user_id', 'name', 'type', 'status', 'value', 'start_date', 'end_date', 'min_total', 'usage_limit', 'usage_count'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function isActive()
    {
        $today = now()->toDateString();
        return (!$this->start_date || $this->start_date <= $today)
            && (!$this->end_date || $this->end_date >= $today);
    }
}
