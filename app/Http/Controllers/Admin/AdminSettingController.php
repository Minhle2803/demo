<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBankSettingRequest;
use App\Http\Requests\Admin\UpdateFeePercentRequest;
use App\Http\Requests\Admin\UpdateLogoRequest;
use App\Http\Requests\Admin\UpdateMinDepositRequest;
use App\Services\Admin\AdminSettingService;
use Illuminate\Support\Facades\Auth;

class AdminSettingController extends Controller
{
    public function index(AdminSettingService $service)
    {
        $bankInfo = $service->getBankInfo();
        $logo = $service->getLogo();
        $feePercent = $service->getFeePercent();
        $minDeposit = $service->getMinDeposit();
        $admin = Auth::user();
        $inviteLink = $admin->invite_code ? route('signup', ['ref' => $admin->invite_code]) : null;

        return view('pages.admin.settings.index', compact('bankInfo', 'logo', 'feePercent', 'minDeposit', 'admin', 'inviteLink'))
            ->with('bank_list', config('bank'));
    }

    public function updateBank(UpdateBankSettingRequest $request, AdminSettingService $service)
    {
        $service->updateBank($request->validated());

        return redirect()->route('admin.settings.index')
            ->with('success', __('admin.bank_updated'));
    }

    public function updateLogo(UpdateLogoRequest $request, AdminSettingService $service)
    {
        $service->updateLogo($request->file('logo'));

        return redirect()->route('admin.settings.index')
            ->with('success', __('admin.logo_updated'));
    }

    public function updateFee(UpdateFeePercentRequest $request, AdminSettingService $service)
    {
        $service->updateFeePercent((float) $request->validated('fee_percent'));

        return redirect()->route('admin.settings.index')
            ->with('success', __('admin.fee_updated'));
    }

    public function updateMinDeposit(UpdateMinDepositRequest $request, AdminSettingService $service)
    {
        $service->updateMinDeposit((float) $request->validated('min_deposit'));

        return redirect()->route('admin.settings.index')
            ->with('success', __('admin.min_deposit_updated'));
    }
}
