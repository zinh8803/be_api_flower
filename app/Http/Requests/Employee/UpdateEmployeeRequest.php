<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateEmployeeRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="Nguyen Van B"),
 *     @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *     @OA\Property(property="phone", type="string", example="0123456789"),
 *     @OA\Property(property="address", type="string", example="123 Street, City"),
 *     @OA\Property(property="status", type="boolean", example=true),
 * )
 */
class UpdateEmployeeRequest extends FormRequest
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
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|unique:users,phone,' . $this->user()->id . '|string|max:15',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Tên nhân viên là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
        ];
    }
}
