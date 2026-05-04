<?php

namespace App\Http\Requests\Internal;

use App\Http\Responses\ApiResponse;
use App\Models\TradingChartCandle;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RewriteCandleRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $symbols = implode(',', config('trading_chart.symbols', ['BTC_USDT', 'ETH_USDT', 'SOL_USDT']));
        $intervals = implode(',', config('trading_chart.intervals', ['1m', '5m']));
        $directions = implode(',', TradingChartCandle::DIRECTIONS);

        return [
            'symbol' => ['required', 'string', "in:{$symbols}"],
            'interval' => ['required', 'string', "in:{$intervals}"],
            'from_timestamp' => ['required', 'integer', 'min:0'],
            'to_timestamp' => ['required', 'integer', 'min:0', 'gt:from_timestamp'],
            'direction' => ['required', 'string', "in:{$directions}"],
            'strength' => ['nullable', 'numeric', 'min:0.1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_timestamp.gt' => 'to_timestamp must be greater than from_timestamp.',
            'direction.in' => 'Direction must be one of: up, down, neutral.',
            'strength.between' => 'strength must be between 0.1 and 10.',
        ];
    }

    public function resolvedStrength(): float
    {
        return (float) ($this->validated()['strength'] ?? 1.0);
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors()->toArray(), ErrorCodes::CHART_INVALID_REQUEST)
        );
    }
}
