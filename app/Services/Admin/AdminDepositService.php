<?php

namespace App\Services\Admin;

use App\Events\BalanceUpdated;
use App\Models\ClientUser;
use App\Models\DepositRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDepositService
{
    public function approve(DepositRequest $deposit): void
    {
        if ($deposit->status !== 'pending') {
            throw new \RuntimeException(__('admin.deposit_already_processed'));
        }

        DB::transaction(function () use ($deposit) {
            $user = ClientUser::findOrFail($deposit->user_id);
            $user->increment('balance', (float) $deposit->amount);

            $deposit->update([
                'status' => 'done',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $freshUser = $user->fresh();

            BalanceUpdated::dispatch(
                $freshUser->id,
                (float) $freshUser->balance,
                'deposit',
                (float) $deposit->amount,
            );
        });
    }

    public function reject(DepositRequest $deposit, string $reason): void
    {
        if ($deposit->status !== 'pending') {
            throw new \RuntimeException(__('admin.deposit_already_processed'));
        }

        $deposit->update([
            'status' => 'rejected',
            'admin_note' => $reason,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);
    }
}
