<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductReport;
use App\Repositories\Contracts\ProductReportRepositoryInterface;

class ProductReportRepository implements ProductReportRepositoryInterface
{
    protected $model;
    public function __construct(ProductReport $model)
    {
        $this->model = $model;
    }
    public function all()
    {
        return $this->model->load(['order', 'orderDetail'])->get();
    }

    public function find($orderId)
    {
        return $this->model->where('order_id', $orderId)->first();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update($id, array $data)
    {
        $report = $this->findById($id);
        if ($report) {
            $report->update($data);
            return $report;
        }
        return null;
    }
    public function delete($id)
    {
        $report = $this->findById($id);
        if ($report) {
            $report->delete();
            return true;
        }
        return false;
    }
}
