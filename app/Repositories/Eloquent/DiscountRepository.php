<?php

namespace App\Repositories\Eloquent;

use App\Models\Discount;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DiscountRepository implements DiscountRepositoryInterface
{
    public function getAll()
    {
        $this->updateExpiredStatus();
        return Discount::orderBy('status', 'desc')
            ->orderBy('start_date', 'asc')
            ->paginate(10);
    }
    public function updateExpiredStatus()
    {
        Discount::where('end_date', '<', now())
            ->where('status', true)
            ->update(['status' => false]);
    }

    public function findById($id)
    {
        return Discount::find($id);
    }

    public function create(array $data)
    {
        return Discount::create($data);
    }

    public function update($id, array $data)
    {
        $discount = Discount::find($id);
        $discount->update($data);
        return $discount;
    }

    public function delete($id)
    {
        return Discount::destroy($id);
    }

    public function checkCodeValidity($code)
    {
        $discount = Discount::where('name', $code)
            ->where('status', true) // Ensure the discount is active
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();
        //Log::info('Checking discount code validity', ['code' => $code, 'result' => $discount]);
        return $discount ? $discount : null;
    }
}
