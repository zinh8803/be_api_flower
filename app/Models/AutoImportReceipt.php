<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoImportReceipt extends Model
{
    protected $fillable = ['import_date', 'details', 'enabled', 'run_time', 'repeat_daily'];
    protected $casts = [
        'details' => 'array',
        'enabled' => 'boolean',
        'repeat_daily' => 'boolean',
    ];
}
