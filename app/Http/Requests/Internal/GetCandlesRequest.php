<?php

namespace App\Http\Requests\Internal;

use App\Http\Responses\ApiResponse;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetCandlesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // guarded at route level by internal middleware
    }

    public function rules(): array
    {
        $symbols = implode(',', config('trading_chart.symbols', ['BTC_USDT', 'ETH_USDT', 'SOL_USDT']));
        $intervals = implode(',', config('trading_chart.intervals', ['1m', '5m']));

        return [
            'symbol' => ['required', 'string', "in:{$symbols}"],
            'interval' => ['required', 'string', "in:{$intervals}"],
            'from' => ['nullable', 'integer', 'min:0'],
            'to' => ['nullable', 'integer', 'min:0', 'gte:from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'symbol.in' => 'Invalid symbol. Supported: '.implode(', ', config('trading_chart.symbols', [])),
            'interval.in' => 'Invalid interval. Supported: '.implode(', ', config('trading_chart.intervals', [])),
            'to.gte' => 'to must be greater than or equal to from.',
        ];
    }

    // Resolved values with defaults applied
    public function resolvedLimit(): int
    {
        return (int) ($this->validated()['limit'] ?? 500);
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors()->toArray(), ErrorCodes::CHART_INVALID_REQUEST)
        );
    }
}
