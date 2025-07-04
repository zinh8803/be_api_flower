<?php

namespace App\Repositories\Eloquent;

use App\Models\ImportReceipt;
use App\Repositories\Contracts\ImportReceiptRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ImportReceiptRepository implements ImportReceiptRepositoryInterface
{
    protected $model;

    public function __construct(ImportReceipt $importReceipt)
    {
        $this->model = $importReceipt;
    }

    public function all($filters = [])
    {
        $query = $this->model->with('details.flower')->orderBy('import_date', 'desc');

        if (!empty($filters['from_date'])) {
            $query->whereDate('import_date', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('import_date', '<=', $filters['to_date']);
        }

        return $query->paginate(10);
    }

    public function find($id)
    {
        return $this->model->with('details.flower')->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
public function createWithDetails(array $data)
{
    return DB::transaction(function () use ($data) {
        $details = $data['details'] ?? [];
        unset($data['details']);

        $total = collect($details)->sum(function ($item) {
            return $item['quantity'] * $item['import_price'];
        });
        $data['total_price'] = $total;

        $receipt = $this->create($data);

        foreach ($details as $detail) {
            $detail['import_date'] = $data['import_date'];
            $detail['subtotal'] = $detail['quantity'] * $detail['import_price'];
            $receipt->details()->create($detail);
        }

        return $this->find($receipt->id);
    });
}


    public function updateWithDetails($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $details = $data['details'] ?? [];
            unset($data['details']);

            $total = collect($details)->sum(function ($item) {
                return $item['quantity'] * $item['import_price'];
            });
            $data['total_price'] = $total;

            $receipt = $this->update($id, $data);

            $receipt->details()->delete();
            foreach ($details as $detail) {
                $detail['subtotal'] = $detail['quantity'] * $detail['import_price'];
                $receipt->details()->create($detail);
            }

            return $this->find($id);
        });
    }
}
