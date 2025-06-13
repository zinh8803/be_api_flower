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
             'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed', // Password must be confirmed
            // 'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Optional avatar image
        ];
    }
}
