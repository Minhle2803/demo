<?php

namespace App\Http\Requests\Auth;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'login' accepts either email or phone number
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required'    => 'Email or phone number is required.',
            'password.required' => 'Password is required.',
        ];
    }

    /**
     * Detect whether 'login' looks like an email or a phone number.
     */
    public function isEmail(): bool
    {
        return filter_var($this->input('login'), FILTER_VALIDATE_EMAIL) !== false;
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

        // WEB FLOW
        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first())
        );
    }
}
