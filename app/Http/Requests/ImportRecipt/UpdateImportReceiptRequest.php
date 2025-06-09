<?php

namespace App\Http\Requests\ImportRecipt;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="ImportReceiptUpdateRequest",
 *     @OA\Property(property="import_date", type="string", format="date", example="2025-06-10"),
 *     @OA\Property(property="note", type="string", example="Chỉnh sửa ghi chú"),
 *     @OA\Property(
 *         property="details",
 *         type="array",
 *         description="Nếu gửi, sẽ thay thế toàn bộ chi tiết bằng mảng mới",
 *         @OA\Items(
 *             @OA\Property(property="flower_id", type="integer", example=3),
 *             @OA\Property(property="quantity", type="integer", example=80),
 *             @OA\Property(property="import_price", type="number", format="double", example=8800)
 *         )
 *     )
 * )
 */
class UpdateImportReceiptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'import_date' => 'sometimes|required|date',
            'note' => 'sometimes|nullable|string|max:255',
            'details' => 'sometimes|array',
            'details.*.flower_id' => 'sometimes|required|integer|exists:flowers,id',
            'details.*.quantity' => 'sometimes|required|integer|min:1',
            'details.*.import_price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|in:hoa tươi,hoa ép', // Optional status field
        ];
    }
}
