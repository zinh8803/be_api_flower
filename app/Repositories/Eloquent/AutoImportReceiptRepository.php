<?php

namespace App\Repositories\Eloquent;

use App\Models\AutoImportReceipt;
use App\Repositories\Contracts\AutoImportReceiptInterface;
use Illuminate\Support\Facades\Log;

class AutoImportReceiptRepository implements AutoImportReceiptInterface
{
    protected $model;

    public function __construct(AutoImportReceipt $autoImportReceipt)
    {
        $this->model = $autoImportReceipt;
    }

    public function all()
    {
        return $this->model->latest('id')->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        Log::info('AutoImportReceipt updated', ['id' => $id, 'data' => $data]);
        return $record;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }
}
