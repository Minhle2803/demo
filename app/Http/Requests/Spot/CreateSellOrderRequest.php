<?php

namespace App\Http\Requests\Spot;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSellOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'symbol' => ['required', 'string', 'in:BTC_USDT,ETH_USDT'],
            'price' => ['required', 'numeric', 'gt:0'],
            'quantity' => ['required', 'numeric', 'gt:0'],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError(
                errors: $validator->errors()->toArray(),
                code: ErrorCodes::SPOT_INVALID_PRICE,
            )
        );
    }
}
