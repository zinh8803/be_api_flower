<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flower extends Model
{
    protected $fillable = ['name', 'price','color','status', 'flower_type_id'];
    public function flowerType()
    {
        return $this->belongsTo(FlowerType::class);
    }
    public function importReceiptDetails()
    {
        return $this->hasMany(ImportReceiptDetail::class);
    }
}
