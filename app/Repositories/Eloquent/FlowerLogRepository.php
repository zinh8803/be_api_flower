<?php

namespace App\Repositories\Eloquent;

use App\Models\FlowerLog;
use App\Repositories\Contracts\FlowerLogRepositoryInterface;

class FlowerLogRepository extends BaseRepository implements FlowerLogRepositoryInterface
{
    public function __construct(FlowerLog $flowerLog)
    {
        parent::__construct($flowerLog);
    }

    public function all($filters = [], ...$args)
    {
        $query = $this->model->with(['flower', 'user'])->orderBy('id', 'desc');

        if (!empty($filters['flower_id'])) {
            $query->where('flower_id', $filters['flower_id']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['change_type'])) {
            $query->where('change_type', $filters['change_type']);
        }
        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    public function getByFlowerId(int $flowerId, array $filters = [])
    {
        $filters['flower_id'] = $flowerId;
        return $this->all($filters);
    }

    public function log(array $data)
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }
        return $this->create($data);
    }
}
