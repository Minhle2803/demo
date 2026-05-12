<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\BalanceUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmDepositRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ClientUser;
use App\Support\ErrorCodes;

class SpotDepositController extends Controller
{
    public function confirm(ConfirmDepositRequest $request)
    {
        $user = ClientUser::where('user_id', $request->input('user_id'))->firstOrFail();

        $amount = $request->input('amount');
        $user->increment('balance', $amount);

        $freshUser = $user->fresh();

        BalanceUpdated::dispatch(
            $freshUser->id,
            (float) $freshUser->balance,
            'deposit',
            (float) $amount,
        );

        return ApiResponse::success(
            data: [
                'user_id' => $freshUser->user_id,
                'new_balance' => (float) $freshUser->balance,
            ],
            code: ErrorCodes::DEPOSIT_CONFIRMED,
        );
    }
}
