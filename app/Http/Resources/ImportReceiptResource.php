<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="ImportReceiptResource",
 *     @OA\Property(property="id", type="integer", example=9),
 *     @OA\Property(property="import_date", type="string", format="date", example="2025-06-09"),
 *     @OA\Property(property="note", type="string", example="Nhập lô hoa hồng"),
 *     @OA\Property(property="total_price", type="number", format="double", example=900000),
 *     @OA\Property(
 *         property="details",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ImportReceiptDetailResource")
 *     )
 * )
 */
class ImportReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       
        return [
           
           'id'          => $this->id,
            'import_code' => $this->import_code,
            'import_date' => $this->import_date ? Carbon::parse($this->import_date)->format('Y-m-d') : null,
            'note'        => $this->note,
            'total_price' => $this->total_price,
            'details'     => ImportReceiptDetailResource::collection($this->whenLoaded('details')),

        ];
    }
}
