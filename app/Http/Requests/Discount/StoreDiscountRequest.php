<?php

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="DiscountStoreRequest",
 *     required={"name,type,value,start_date.end_date"},
 *     @OA\Property(property="name", type="string", example="HOA10"),
 *     @OA\Property(property="type", type="string", example="percent"),
 *      @OA\Property(property="value", type="float", example=10),
 *     @OA\Property(property="start_date", type="string", format="date-time", example="2025-06-01"),
 *     @OA\Property(property="end_date", type="string", format="date-time", example="2025-06-01"),
 * )
 */
class StoreDiscountRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:discounts,name',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }
}
