<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportReceipt extends Model
{
    protected $fillable = ['supplier_id', 'import_date', 'total_amount'];

 public function user()
{
    return $this->belongsTo(User::class);
}

public function details()
{
    return $this->hasMany(ImportReceiptDetail::class);
}
}
