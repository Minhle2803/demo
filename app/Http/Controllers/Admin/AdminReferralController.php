<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use App\Services\Referral\ReferralStatsService;
use Illuminate\Http\Request;

class AdminReferralController extends Controller
{
    public function __construct(
        protected ReferralStatsService $statsService,
    ) {}

    public function index(Request $request)
    {
        $admin = $request->user();

        $stats = $this->statsService->getAdminStats($admin->id);
        $invitedUsers = $this->statsService->getAdminInvitedUsers($admin->id);
        $inviteLink = $admin->invite_code
            ? config('app.url').'/signup?ref='.$admin->invite_code
            : null;

        return view('pages.admin.referrals.index', compact('stats', 'invitedUsers', 'inviteLink', 'admin'));
    }

    public function show(int $clientUserId, Request $request)
    {
        $admin = $request->user();
        $agent = ClientUser::where('invited_by_admin_id', $admin->id)
            ->with('invitedByClient')
            ->findOrFail($clientUserId);

        $stats = $this->statsService->getAgentStats($agent->id);
        $invitedUsers = $this->statsService->getAgentInvitedUsers($agent->id);

        return view('pages.admin.referrals.show', compact('agent', 'stats', 'invitedUsers', 'admin'));
    }
}
