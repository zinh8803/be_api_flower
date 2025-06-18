<?php
namespace App\Repositories\Eloquent;

use App\Models\Discount;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use Log;

class DiscountRepository implements DiscountRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        $query = Discount::query();

        if (!empty($filters['active_only'])) {
            $query->whereDate('start_date', '<=', now())
                  ->whereDate('end_date', '>=', now());
        }

        return $query->get();
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
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();
       //Log::info('Checking discount code validity', ['code' => $code, 'result' => $discount]);
        return $discount ? $discount : null;
    }
}
