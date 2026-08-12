<?php

namespace App\Repositories\Eloquent;

use App\Models\AutoImportReceipt;
use App\Repositories\Contracts\AutoImportReceiptInterface;
use Illuminate\Support\Facades\Log;

class AutoImportReceiptRepository extends BaseRepository implements AutoImportReceiptInterface
{
    public function __construct(AutoImportReceipt $autoImportReceipt)
    {
        parent::__construct($autoImportReceipt);
    }

    public function all($columns = ['*'], ...$args)
    {
        $latest = $this->model->latest('id')->first();
        return $latest ? new \Illuminate\Database\Eloquent\Collection([$latest]) : new \Illuminate\Database\Eloquent\Collection();
    }

    public function update($id, array $data)
    {
        $record = $this->findById($id);
        $record->update($data);
        Log::info('AutoImportReceipt updated', ['id' => $id, 'data' => $data]);
        return $record;
    }
}
