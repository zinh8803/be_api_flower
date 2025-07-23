<?php

namespace App\Http\Requests\ProductReport;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="ProductReportItem",
 *     type="object",
 *     required={"order_id", "order_detail_id", "quantity", "reason"},
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="order_detail_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="reason", type="string", example="Product damaged"),
 *     @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg"),
 *     @OA\Property(property="action", type="string", example="Đổi hàng"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="StoreProductReportRequest",
 *     type="object",
 *     required={"reports"},
 *     @OA\Property(
 *         property="reports",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ProductReportItem")
 *     )
 * )
 */
class StoreProductReportRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'reports' => 'required|array|min:1',
            'reports.*.order_id' => 'required|exists:orders,id',
            'reports.*.order_detail_id' => 'required|exists:order_details,id',
            'reports.*.quantity' => 'required|integer|min:1',
            'reports.*.reason' => 'required|string|max:255',
            'reports.*.image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'reports.*.action' => 'sometimes|string',
        ];
    }
}
