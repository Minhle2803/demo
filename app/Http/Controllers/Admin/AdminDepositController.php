<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveDepositRequest;
use App\Http\Requests\Admin\RejectDepositRequest;
use App\Models\DepositRequest;
use App\Services\Admin\AdminDepositService;

class AdminDepositController extends Controller
{
    public function index()
    {
        $deposits = DepositRequest::with('user')
            ->latest()
            ->paginate(20);

        return view('pages.admin.deposits.index', compact('deposits'));
    }

    public function approve(ApproveDepositRequest $request, int $id, AdminDepositService $service)
    {
        $deposit = DepositRequest::findOrFail($id);
        $service->approve($deposit);

        return redirect()->route('admin.deposits.index')
            ->with('success', __('admin.deposit_approved'));
    }

    public function reject(RejectDepositRequest $request, int $id, AdminDepositService $service)
    {
        $deposit = DepositRequest::findOrFail($id);
        $service->reject($deposit, $request->input('reason'));

        return redirect()->route('admin.deposits.index')
            ->with('success', __('admin.deposit_rejected'));
    }
}
