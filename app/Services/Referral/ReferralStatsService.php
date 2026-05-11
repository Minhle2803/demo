<?php

namespace App\Services\Referral;

use App\Models\ClientUser;
use App\Models\Trade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReferralStatsService
{
    /**
     * Get aggregated stats for users under a given admin.
     */
    public function getAdminStats(int $adminId): array
    {
        $userIds = ClientUser::where('invited_by_admin_id', $adminId)->whereNull('invited_by_client_id')->pluck('id');

        return $this->aggregateStats($userIds);
    }

    /**
     * Get aggregated stats for users under a given client/agent.
     */
    public function getAgentStats(int $clientUserId): array
    {
        $userIds = ClientUser::where('invited_by_client_id', $clientUserId)->pluck('id');

        return $this->aggregateStats($userIds);
    }

    /**
     * Get paginated list of users invited by a given admin.
     */
    public function getAdminInvitedUsers(int $adminId, int $perPage = 25): LengthAwarePaginator
    {
        return ClientUser::where('invited_by_admin_id', $adminId)
            ->whereNull('invited_by_client_id')
            ->withSum(['trades as total_played' => function ($q) {
                $q->whereIn('status', ['win', 'lose']);
            }], 'amount')
            ->withSum(['trades as total_won' => function ($q) {
                $q->where('status', 'win');
            }], 'payout')
            ->withSum(['trades as total_fees' => function ($q) {
                $q->where('status', 'win');
            }], 'trading_fee')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get paginated list of users invited by a given agent.
     */
    public function getAgentInvitedUsers(int $clientUserId, int $perPage = 25): LengthAwarePaginator
    {
        return ClientUser::where('invited_by_client_id', $clientUserId)
            ->withSum(['trades as total_played' => function ($q) {
                $q->whereIn('status', ['win', 'lose']);
            }], 'amount')
            ->withSum(['trades as total_won' => function ($q) {
                $q->where('status', 'win');
            }], 'payout')
            ->withSum(['trades as total_fees' => function ($q) {
                $q->where('status', 'win');
            }], 'trading_fee')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Aggregate trade stats for a set of user IDs.
     */
    protected function aggregateStats($userIds): array
    {
        if ($userIds->isEmpty()) {
            return [
                'total_played' => 0,
                'total_won' => 0,
                'total_fees' => 0,
            ];
        }

        $result = Trade::whereIn('user_id', $userIds)
            ->select([
                DB::raw('COALESCE(SUM(CASE WHEN status IN ("win", "lose") THEN amount ELSE 0 END), 0) as total_played'),
                DB::raw('COALESCE(SUM(CASE WHEN status = "win" THEN payout ELSE 0 END), 0) as total_won'),
                DB::raw('COALESCE(SUM(CASE WHEN status = "win" THEN trading_fee ELSE 0 END), 0) as total_fees'),
            ])
            ->first();

        return [
            'total_played' => (float) $result->total_played,
            'total_won' => (float) $result->total_won,
            'total_fees' => (float) $result->total_fees,
        ];
    }
}
