<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flower extends Model
{
    protected $fillable = ['name', 'price', 'color_id', 'flower_type_id'];
    public function flowerType()
    {
        return $this->belongsTo(FlowerType::class);
    }
    public function importReceiptDetails()
    {
        return $this->hasMany(ImportReceiptDetail::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
