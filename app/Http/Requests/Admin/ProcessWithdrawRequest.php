<?php

namespace App\Http\Requests\Admin;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProcessWithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'withdraw_id' => ['required', 'integer', 'exists:withdraw_requests,id'],
            'status' => ['required', 'string', 'in:done,pending,rejected'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError(
                errors: $validator->errors()->toArray(),
                code: ErrorCodes::AUTH_VALIDATION_ERROR,
            )
        );
    }
}
