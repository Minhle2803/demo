<?php

namespace App\Http\Requests\Auth;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public registration endpoint
    }

    public function rules(): array
    {
        return [
            'nickname' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:client_users,nickname',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:client_users,email',
            ],
            'phone_number' => [
                'required',
                'string',
                // E.164-ish rule: + followed by 7-15 digits. Adjust to your market.
                'regex:/^\+?[0-9]{7,15}$/',
                'unique:client_users,phone_number',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Strong password: at least one uppercase, lowercase, digit, special char
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
            'referral_code' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nickname.unique' => __('errors.'.ErrorCodes::AUTH_NICKNAME_ALREADY_USED),
            'email.unique' => __('errors.'.ErrorCodes::AUTH_EMAIL_ALREADY_USED),
            'phone_number.unique' => __('errors.'.ErrorCodes::AUTH_PHONE_ALREADY_USED),
            'phone_number.regex' => 'The phone number format is invalid.',
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ];
    }

    /**
     * Throw a structured JSON error instead of redirecting on validation failure.
     * This is an API-first implementation.
     */
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
