<?php

namespace App\Http\Controllers\Api\Admin;

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

        return ApiResponse::success(
            data: [
                'user_id' => $user->user_id,
                'new_balance' => (float) $user->fresh()->balance,
            ],
            code: ErrorCodes::DEPOSIT_CONFIRMED,
        );
    }
}
