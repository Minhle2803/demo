<?php

namespace App\Http\Requests\Profile;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GenerateDepositQrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => __('errors.'.ErrorCodes::DEPOSIT_AMOUNT_REQUIRED),
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Minimum deposit amount is 10,000 VND.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                ApiResponse::validationError(
                    errors: $validator->errors()->toArray(),
                    code: ErrorCodes::AUTH_VALIDATION_ERROR,
                )
            );
        }
        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first())
        );
    }
}
