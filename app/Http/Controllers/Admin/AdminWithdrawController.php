<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveWithdrawRequest;
use App\Http\Requests\Admin\RejectWithdrawRequest;
use App\Models\WithdrawRequest;
use App\Services\Admin\AdminWithdrawService;

class AdminWithdrawController extends Controller
{
    public function index()
    {
        $withdraws = WithdrawRequest::with('user')
            ->latest()
            ->paginate(20);

        return view('pages.admin.withdraws.index', compact('withdraws'));
    }

    public function approve(ApproveWithdrawRequest $request, int $id, AdminWithdrawService $service)
    {
        $withdraw = WithdrawRequest::findOrFail($id);
        $service->approve($withdraw);

        return redirect()->route('admin.withdraws.index')
            ->with('success', __('admin.withdraw_approved'));
    }

    public function reject(RejectWithdrawRequest $request, int $id, AdminWithdrawService $service)
    {
        $withdraw = WithdrawRequest::findOrFail($id);
        $service->reject($withdraw, $request->input('reason'));

        return redirect()->route('admin.withdraws.index')
            ->with('success', __('admin.withdraw_rejected'));
    }
}
