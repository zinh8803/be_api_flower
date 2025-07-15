<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *     schema="ImportReceiptDetailResource",
 *     @OA\Property(property="id", type="integer", example=15),
 *     @OA\Property(property="flower_id", type="integer", example=3),
 *     @OA\Property(property="flower_name", type="string", example="Hoa hồng đỏ"),
 *     @OA\Property(property="quantity", type="integer", example=100),
 *      @OA\Property(property="used_quantity", type="integer", example=20),
 *     @OA\Property(property="remaining_quantity", type="integer", example=80),
 *     @OA\Property(property="import_price", type="number", format="double", example=9000),
 *     @OA\Property(property="import_date", type="string", format="date", example="2025-06-09"),
 *     @OA\Property(property="subtotal", type="number", format="double", example=900000)
 * )
 */
class ImportReceiptDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = 'hoa tươi';

        if (!empty($this->import_date)) {
            $importDate = Carbon::parse($this->import_date);
            $today = Carbon::today();
            $now = Carbon::now();

            $nextDayAfterImport = $importDate->copy()->addDay()->setHour(22)->setMinute(0)->setSecond(0);

            if ($now->gte($nextDayAfterImport)) {
            $status = 'hoa ép';
            }
        }



        return [
            'id'          => $this->id,
            'flower_id'   => $this->flower_id,
            'flower_name' => $this->flower->name,
            'quantity'    => $this->quantity,
            'used_quantity' => $this->used_quantity,
            'remaining_quantity' => $this->quantity - $this->used_quantity,
            'import_price'  => $this->import_price,
            'subtotal'    => $this->subtotal,
            'import_date' => $this->import_date,
            'status' => $status,

        ];
    }
}
