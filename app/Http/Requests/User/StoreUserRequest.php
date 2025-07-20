<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     required={"name", "email", "password", "password_confirmation"},
 *     @OA\Property(property="name", type="string", example="Nguyen Van A"),
 *     @OA\Property(property="email", type="string", format="email", example="a@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="123456"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="123456"),
 *     @OA\Property(property="phone", type="string", example="0909123456"),
 *     @OA\Property(property="otp", type="string", format="binary", description="One-time password for email verification"),
 * )
 */

class StoreUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'nullable|unique:users,phone|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'role' => 'nullable|in:admin,user',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Email này đã được đăng ký. Vui lòng dùng email khác.',
            'phone.unique' => 'Số điện thoại này đã được đăng ký. Vui lòng dùng số khác.',
        ];
    }
}
