<?php

namespace App\Services\Admin;

use App\Events\BalanceUpdated;
use App\Models\ClientUser;
use App\Models\WithdrawRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminWithdrawService
{
    public function approve(WithdrawRequest $withdraw): void
    {
        if (! in_array($withdraw->status, ['pending', 'processing'], true)) {
            throw new \RuntimeException(__('admin.withdraw_already_processed'));
        }

        DB::transaction(function () use ($withdraw) {


            $withdraw->update([
                'status' => 'done',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);
        });
    }

    public function reject(WithdrawRequest $withdraw, string $reason): void
    {
        if (! in_array($withdraw->status, ['pending', 'processing'], true)) {
            throw new \RuntimeException(__('admin.withdraw_already_processed'));
        }
        $user = ClientUser::findOrFail($withdraw->user_id);

        $user->increment('balance', (float) $withdraw->amount);

        $withdraw->update([
            'status' => 'rejected',
            'admin_note' => $reason,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        $freshUser = $user->fresh();

        BalanceUpdated::dispatch(
            $freshUser->id,
            (float) $freshUser->balance,
            'withdraw',
            (float) $withdraw->amount,
        );
    }
}

