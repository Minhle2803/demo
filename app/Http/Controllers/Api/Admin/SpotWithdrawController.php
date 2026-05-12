<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\BalanceUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessWithdrawRequest;
use App\Http\Responses\ApiResponse;
use App\Models\WithdrawRequest;
use App\Support\ErrorCodes;

class SpotWithdrawController extends Controller
{
    public function process(ProcessWithdrawRequest $request)
    {
        $withdraw = WithdrawRequest::with('user')->findOrFail($request->input('withdraw_id'));

        if (! in_array($withdraw->status, ['processing', 'pending'])) {
            return ApiResponse::error(
                code: 'WITHDRAW_ALREADY_PROCESSED',
                message: 'This withdrawal request has already been processed.',
                statusCode: 400,
            );
        }

        $status = $request->input('status');

        if ($status === 'done' && $withdraw->user) {
            $withdraw->user->decrement('balance', $withdraw->amount);

            $freshUser = $withdraw->user->fresh();

            BalanceUpdated::dispatch(
                $freshUser->id,
                (float) $freshUser->balance,
                'withdraw',
                (float) $withdraw->amount,
            );
        }

        $withdraw->update([
            'status' => $status,
            'admin_note' => $request->input('admin_note'),
            'processed_by' => $request->user()?->id,
            'processed_at' => now(),
        ]);

        return ApiResponse::success(
            data: [
                'withdraw_id' => $withdraw->id,
                'status' => $withdraw->status,
                'user_id' => $withdraw->user->user_id ?? null,
                'new_balance' => $withdraw->user ? (float) $withdraw->user->fresh()->balance : null,
            ],
            code: ErrorCodes::WITHDRAW_PROCESSED,
        );
    }
}
