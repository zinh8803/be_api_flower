<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportReceiptDetail extends Model
{
    protected $fillable = ['import_receipt_id', 'product_id', 'quantity', 'price'];

    public function importReceipt()
    {
        return $this->belongsTo(ImportReceipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
