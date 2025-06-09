<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportReceipt extends Model
{
    protected $fillable = ['note','import_date','total_price','user_id'];

 public function user()
{
    return $this->belongsTo(User::class);
}

public function details()
{
    return $this->hasMany(ImportReceiptDetail::class);
}
}
