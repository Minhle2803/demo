<?php

namespace App\Http\Requests\Profile;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitKycRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kyc_front' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'kyc_back' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'cccd_number' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'kyc_front.required' => __('errors.'.ErrorCodes::KYC_UPLOAD_REQUIRED),
            'kyc_back.required' => __('errors.'.ErrorCodes::KYC_UPLOAD_REQUIRED),
            'kyc_front.mimes' => 'ID image must be JPG, JPEG, or PNG.',
            'kyc_back.mimes' => 'ID image must be JPG, JPEG, or PNG.',
            'kyc_front.max' => 'ID image must not exceed 5MB.',
            'kyc_back.max' => 'ID image must not exceed 5MB.',
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
