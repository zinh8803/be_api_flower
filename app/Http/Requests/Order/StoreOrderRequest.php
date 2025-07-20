<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="OrderStoreRequest",
 *     type="object",
 *     required={"name", "email", "phone", "address", "payment_method", "products"},
 *     @OA\Property(property="name", type="string", example="Nguyen Van A"),
 *     @OA\Property(property="email", type="string", example="a@example.com"),
 *     @OA\Property(property="phone", type="string", example="0123456789"),
 *     @OA\Property(property="address", type="string", example="123 Nguyen Trai"),
 *     @OA\Property(property="note", type="string", example="Giao trong giờ hành chính", nullable=true),
 *     @OA\Property(property="payment_method", type="string", example="cod"),
 *      @OA\Property(property="user_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="discount_id", type="integer", example=null, nullable=true),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="product_size_id", type="integer", example=2),
 *             @OA\Property(property="quantity", type="integer", example=2)
 *         )
 *     ) 
 * )
 */
class StoreOrderRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000',
            'discount_id' => 'nullable|integer',
            'payment_method' => 'required|string|in:cod,vnpay',
            'user_id' => 'nullable|integer|exists:users,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.product_size_id' => 'required|integer|exists:product_sizes,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'payment_method.required' => 'Phương thức thanh toán là bắt buộc.',
        ];
    }
}
