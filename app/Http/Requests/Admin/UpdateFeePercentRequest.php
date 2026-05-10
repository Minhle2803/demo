<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeePercentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'fee_percent.required' => __('admin.fee_percent_required'),
            'fee_percent.numeric' => __('admin.fee_percent_numeric'),
            'fee_percent.min' => __('admin.fee_percent_min'),
            'fee_percent.max' => __('admin.fee_percent_max'),
        ];
    }
}
