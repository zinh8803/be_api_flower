<?php

namespace App\Http\Requests\AutoImportReceipt;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutoImportReceipt extends FormRequest
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
            'import_date' => 'required|date_format:Y-m-d',
            'details'   => 'present|array',
            'details.*.flower_id' => 'required|exists:flowers,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.import_price' => 'required|numeric|min:0',
            'repeat_daily' => 'sometimes|boolean',
            'run_time'  => 'required|date_format:H:i',
            'enabled'   => 'required|boolean',
            'note'      => 'nullable|string|max:255',
        ];
    }
}
