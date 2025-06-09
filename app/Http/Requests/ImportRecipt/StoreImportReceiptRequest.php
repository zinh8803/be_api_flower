<?php

namespace App\Http\Requests\ImportRecipt;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="ImportReceiptStoreRequest",
 *     required={"import_date", "details"},
 *     @OA\Property(property="import_date", type="string", format="date", example="2025-06-09"),
 *     @OA\Property(property="note", type="string", example="Nhập lô hoa hồng"),
 *     @OA\Property(
 *         property="details",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="flower_id", type="integer", example=3),
 *             @OA\Property(property="quantity", type="integer", example=100),
 *             @OA\Property(property="import_price", type="number", format="double", example=9000),
 *              @OA\Property(property="status", type="string", example="hoa tươi") 
 *         )
 *     )
 * )
 */
class StoreImportReceiptRequest extends FormRequest
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
            'import_date' => 'required|date',
            'note' => 'nullable|string|max:255',
            'details' => 'required|array',
            'details.*.flower_id' => 'required|integer|exists:flowers,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.import_price' => 'required|numeric|min:0',
            'status' => 'sometimes|in:hoa tươi,hoa ép',
        ];
    }
}
