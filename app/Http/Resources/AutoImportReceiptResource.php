<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transform the resource into an array.
 *
 * @return array<string, mixed>
 */
/**
 * @OA\Schema(
 *     schema="AutoImportReceipt",
 *     type="object",
 *     title="AutoImportReceipt",
 *     required={"id", "import_date", "details", "enabled", "run_time"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="import_date", type="string", format="date", example="2025-06-01"),
 *     @OA\Property(property="details", type="array", @OA\Items(type="string"), example={"Detail 1", "Detail 2"}),
 *     @OA\Property(property="enabled", type="boolean", example=true),
 *     @OA\Property(property="run_time", type="string", format="time", example="12:00:00")
 * )
 */
class AutoImportReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'import_date' => $this->import_date,
            'details' => $this->details,
            'enabled' => $this->enabled,
            'run_time' => $this->run_time,
        ];
    }
}
