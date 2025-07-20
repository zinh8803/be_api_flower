<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreEmployeeRequest",
 *     required={"name", "email", "password", "role"},
 *     @OA\Property(property="name", type="string", example="Nguyen Van A"),
 *     @OA\Property(property="email", type="string", format="email", example="nguyen@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123"),
 *     @OA\Property(property="role", type="string", enum={"employee"}, example="employee"),
 *     @OA\Property(property="status", type="integer", enum={0, 1}, example=1),
 * )
 */
class StoreEmployeeRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            //'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:employee',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Tên nhân viên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            // 'password.required' => 'Mật khẩu là bắt buộc.',
            // 'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'role.required' => 'Vai trò là bắt buộc.',
            'role.in' => 'Vai trò không hợp lệ.',
        ];
    }
}
