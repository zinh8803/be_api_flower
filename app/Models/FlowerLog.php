<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowerLog extends Model
{
    protected $fillable = [
        'flower_id',
        'type',
        'change_type',
        'quantity',
        'note',
        'reference_type',
        'reference_id',
        'user_id',
    ];

    public function flower()
    {
        return $this->belongsTo(Flower::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
