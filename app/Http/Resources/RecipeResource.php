<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="RecipeDetailResource",
 *     @OA\Property(property="id", type="integer", example=15),
 *     @OA\Property(property="flower_id", type="integer", example=3),
 *     @OA\Property(property="flower_name", type="string", example="Hoa hồng đỏ"),
 *     @OA\Property(property="quantity", type="integer", example=100),
 *     @OA\Property(property="import_price", type="number", format="double", example=9000),
 *      @OA\Property(property="import_date", type="string", format="date", example="2025-06-09"),
 *     @OA\Property(property="subtotal", type="number", format="double", example=900000)
 * )
 */
class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $importReceiptDetail = null;
        if ($this->flower && $this->flower->importReceiptDetails && $this->flower->importReceiptDetails->count()) {
            $importReceiptDetail = $this->flower->importReceiptDetails->sortByDesc('import_date')->first();
        }
 $status = null;

    if ($importReceiptDetail && !empty($importReceiptDetail->import_date)) {
        $importDate = Carbon::parse($importReceiptDetail->import_date);
        $today = Carbon::today();

        if ($importDate->isSameDay($today)) {
            $now = Carbon::now();
            $status = $now->hour >= 22 ? 'hoa ép' : 'hoa tươi';
        } elseif ($importDate->lt($today)) {
            $status = 'hoa ép';
        }
    }


        return [
            'flower_id'     => $this->flower_id,
            'flower_name'   => $this->flower->name ?? null,
            'quantity'      => $this->quantity,
            'import_price'  => $importReceiptDetail->import_price ?? null,
            'import_date'   => $importReceiptDetail->import_date ?? null,
            'status'        => $status,
        ];
    }
}
