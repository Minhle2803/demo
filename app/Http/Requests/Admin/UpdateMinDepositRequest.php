<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMinDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'min_deposit' => ['required', 'numeric', 'min:10000', 'max:100000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'min_deposit.required' => __('admin.min_deposit_required'),
            'min_deposit.numeric' => __('admin.min_deposit_numeric'),
            'min_deposit.min' => __('admin.min_deposit_min'),
            'min_deposit.max' => __('admin.min_deposit_max'),
        ];
    }
}
