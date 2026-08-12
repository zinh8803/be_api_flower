<?php

namespace App\Repositories\Eloquent;

use App\Jobs\sendDiscount;
use App\Models\Discount;
use App\Models\User;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DiscountRepository extends BaseRepository implements DiscountRepositoryInterface
{
    public function __construct(Discount $discount)
    {
        parent::__construct($discount);
    }

    public function getAll($filters = [])
    {
        $this->updateExpiredStatus();

        $query = $this->model->newQuery();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }

        return $query->orderBy('status', 'desc')
            ->orderBy('start_date', 'asc')
            ->paginate(10);
    }

    public function updateExpiredStatus()
    {
        $this->model->where('end_date', '<', now())
            ->where('status', true)
            ->update(['status' => false]);
        $this->model->whereNotNull('usage_limit')
            ->whereColumn('usage_count', '>=', 'usage_limit')
            ->where('status', true)
            ->update(['status' => false]);
    }

    public function checkCodeValidity($code, $userId = null)
    {
        $discount = $this->model->where('name', $code)->first();
        Log::info('discounts', ['code' => $code, 'discount' => $discount]);
        if (!$discount) {
            return ['status' => false, 'reason' => 'not_found'];
        }

        if (!is_null($discount->user_id) && $discount->user_id != $userId) {
            return ['status' => false, 'reason' => 'not_allowed'];
        }

        if (now()->lt($discount->start_date) || now()->gt($discount->end_date)) {
            return ['status' => false, 'reason' => 'expired'];
        }

        if (!is_null($discount->usage_limit) && $discount->usage_count >= $discount->usage_limit) {
            return ['status' => false, 'reason' => 'usage_limit'];
        }

        return ['status' => true, 'discount' => $discount];
    }

    public function sentDiscountEmail($discounts)
    {
        $users = User::where('is_subscribe', true)->get();

        foreach ($users as $user) {
            sendDiscount::dispatch($discounts, $user->email);
        }
    }

    public function getDiscountStats()
    {
        $now = now();

        $total = $this->model->count();
        $active = $this->model->where('status', true)
            ->where('end_date', '>=', $now)
            ->count();
        $expired = $this->model->where(function ($q) use ($now) {
            $q->where('status', false)
                ->orWhere('end_date', '<', $now);
        })
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
        ];
    }
}
