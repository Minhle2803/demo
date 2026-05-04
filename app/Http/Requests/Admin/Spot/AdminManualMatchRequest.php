<?php

namespace App\Http\Requests\Admin\Spot;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminManualMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => ['required', 'numeric', 'gt:0'],
            'quantity' => ['required', 'numeric', 'gt:0'],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError(
                errors: $validator->errors()->toArray(),
                code: ErrorCodes::SPOT_ADMIN_MATCH_FAILED,
            )
        );
    }
}
