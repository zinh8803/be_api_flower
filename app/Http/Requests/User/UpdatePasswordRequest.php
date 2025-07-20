<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdatePasswordRequest",
 *     required={"old_password", "new_password"},
 *     @OA\Property(property="old_password", type="string", example="OldPassword123"),
 *     @OA\Property(property="new_password", type="string", example="NewPassword123"),
 *     @OA\Property(property="new_password_confirmation", type="string", example="NewPassword123"), 
 * )
 */
class UpdatePasswordRequest extends FormRequest
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
            'old_password' => 'required|string|min:5',
            'new_password' => 'required|string|min:6|confirmed',
        ];
    }
    public function messages(): array
    {
        return [
            'old_password.required' => 'Mật khẩu cũ là bắt buộc.',
            'new_password.required' => 'Mật khẩu mới là bắt buộc.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ];
    }
}
