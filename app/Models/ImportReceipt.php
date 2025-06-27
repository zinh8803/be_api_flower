<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ImportReceipt extends Model
{
    protected $fillable = ['import_code', 'note', 'import_date', 'total_price', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(ImportReceiptDetail::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->import_code)) {
                do {
                    $importCode = 'PN' . date('YmdHis') . rand(100, 999);
                } while (
                    DB::table('import_receipts')->where('import_code', $importCode)->exists()
                );
                $model->import_code = $importCode;
            }
        });
    }

}
