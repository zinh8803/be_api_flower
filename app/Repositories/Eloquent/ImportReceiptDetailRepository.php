<?php

namespace App\Repositories\Eloquent;

use App\Models\ImportReceiptDetail;
use App\Repositories\Contracts\ImportReceiptDetailRepositoryInterface;

class ImportReceiptDetailRepository extends BaseRepository implements ImportReceiptDetailRepositoryInterface
{
    public function __construct(ImportReceiptDetail $model)
    {
        parent::__construct($model);
    }
}
