<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportReceiptDetail extends Model
{
    protected $fillable = ['quantity','import_price','import_date','subtotal','import_receipt_id','flower_id'];

    public function importReceipt()
    {
        return $this->belongsTo(ImportReceipt::class);
    }

    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }
    public function flower()
{
    return $this->belongsTo(Flower::class);
}
}
