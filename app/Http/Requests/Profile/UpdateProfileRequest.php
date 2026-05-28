<?php

namespace App\Http\Requests\Profile;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_name' => ['nullable', 'string', 'max:255'],
            'bank_number' => ['nullable', 'string', 'max:50'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'cccd_number' => ['nullable', 'string', 'max:50'],
            'kyc_front' => ['nullable', 'file', 'image', 'max:10240'],
            'kyc_back' => ['nullable', 'file', 'image', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'nickname.unique' => __('errors.'.ErrorCodes::AUTH_NICKNAME_ALREADY_USED),
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
