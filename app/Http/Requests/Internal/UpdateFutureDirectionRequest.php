<?php

namespace App\Http\Requests\Internal;

use App\Http\Responses\ApiResponse;
use App\Models\TradingChartCandle;
use App\Support\ErrorCodes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFutureDirectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $symbols   = implode(',', config('trading_chart.symbols', ['BTC_USDT', 'ETH_USDT', 'SOL_USDT']));
        $intervals = implode(',', config('trading_chart.intervals', ['1m', '5m']));
        $directions = implode(',', TradingChartCandle::DIRECTIONS);

        return [
            'symbol'             => ['required', 'string', "in:{$symbols}"],
            'interval'           => ['required', 'string', "in:{$intervals}"],
            'direction'          => ['required', 'string', "in:{$directions}"],
            'from_timestamp'     => ['nullable', 'integer', 'min:0'],
            'to_timestamp'       => ['nullable', 'integer', 'min:0', 'gte:from_timestamp'],
            'price_bias_percent' => ['nullable', 'numeric', 'between:-100,100'],
            'apply_to_existing'  => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'direction.in'          => 'Direction must be one of: up, down, neutral.',
            'symbol.in'             => 'Invalid symbol.',
            'interval.in'           => 'Invalid interval.',
            'to_timestamp.gte'      => 'to_timestamp must be >= from_timestamp.',
            'price_bias_percent.between' => 'price_bias_percent must be between -100 and 100.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors()->toArray(), ErrorCodes::CHART_INVALID_REQUEST)
        );
    }
}
