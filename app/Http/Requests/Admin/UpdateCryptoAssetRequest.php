<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCryptoAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'symbol' => ['required', 'string', 'max:20', 'unique:crypto_assets,symbol,'.$this->route('id')],
            'name' => ['required', 'string', 'max:255'],
            'icon_url' => ['nullable', 'string', 'max:255'],
            'base_asset' => ['required', 'string', 'max:10'],
            'quote_asset' => ['required', 'string', 'max:10'],
            'price' => ['nullable', 'numeric'],
            'price_precision' => ['nullable', 'integer', 'min:0', 'max:18'],
            'quantity_precision' => ['nullable', 'integer', 'min:0', 'max:18'],
            'min_quantity' => ['nullable', 'numeric', 'min:0'],
            'min_notional' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}
