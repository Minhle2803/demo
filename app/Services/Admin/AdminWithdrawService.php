<?php

namespace App\Services\Admin;

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
            $user = ClientUser::findOrFail($withdraw->user_id);

            if (bccomp((string) $user->balance, (string) $withdraw->amount, 2) < 0) {
                throw new \RuntimeException(__('admin.insufficient_balance'));
            }

            $user->decrement('balance', (float) $withdraw->amount);

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

        $withdraw->update([
            'status' => 'rejected',
            'admin_note' => $reason,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);
    }
}
