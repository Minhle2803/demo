<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveWithdrawRequest;
use App\Http\Requests\Admin\RejectWithdrawRequest;
use App\Models\WithdrawRequest;
use App\Services\Admin\AdminWithdrawService;
use Illuminate\Http\Request;

class AdminWithdrawController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search', '');
        $withdraws = WithdrawRequest::with('user')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('nickname', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(20);

        return view('pages.admin.withdraws.index', compact('withdraws', 'search'));
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
