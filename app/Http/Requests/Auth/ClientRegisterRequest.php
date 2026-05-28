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
                'regex:/^[a-zA-Z0-9_]+$/', // Chỉ cho phép chữ, số, dấu gạch dưới
                'unique:client_users,nickname',
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
            'phone_number.unique' => __('errors.'.ErrorCodes::AUTH_PHONE_ALREADY_USED),
            'phone_number.regex' => 'Số điện thoại không đúng.',
            'password.confirmed' => 'Xác nhận mật khẩu phải giống với mật khẩu.',
            'nickname.regex' => 'Tên người dùng chỉ được chứa chữ cái, số và dấu gạch dưới (_), không được có dấu cách.',
            'nickname.unique' => 'Tên người dùng đã được sử dụng.',
            'nickname.min' => 'Tên người dùng phải có ít nhất :min ký tự.',
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
