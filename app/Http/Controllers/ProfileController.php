<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\GenerateDepositQrRequest;
use App\Http\Requests\Profile\SubmitKycRequest;
use App\Http\Requests\Profile\SubmitWithdrawRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Responses\ApiResponse;
use App\Models\DepositRequest;
use App\Models\ProjectSetting;
use App\Models\WithdrawRequest;
use App\Services\ProfileService;
use App\Support\ErrorCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService) {}

    public function show(Request $request)
    {
        $user = Auth::guard('client')->user();
        $bank_name = ProjectSetting::getValue('deposit_bank_name');
        $bank_account = ProjectSetting::getValue('deposit_bank_account');
        $bank_number = ProjectSetting::getValue('deposit_bank_number');

        return view('pages.profile', [
            'user' => $user,
            'activeTab' => $request->query('tab', 'profile'),
            'bank_name' => $bank_name,
            'bank_account' => $bank_account,
            'bank_number' => $bank_number,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('client')->user();
        $this->profileService->updateProfile($user, $request->validated());

        if ($request->expectsJson()) {
            return ApiResponse::success(
                data: ['nickname' => $user->nickname],
                code: ErrorCodes::PROFILE_UPDATED,
            );
        }

        return redirect()
            ->back()
            ->with('success', __('errors.'.ErrorCodes::PROFILE_UPDATED));
    }

    public function updateBankInfo(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('client')->user();

        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:255'],
            'bank_number' => ['required', 'string', 'max:50'],
            'bank_account' => ['required', 'string', 'max:255'],
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return ApiResponse::success(
                data: [
                    'account_name' => $user->account_name,
                    'bank_number' => $user->bank_number,
                    'bank_account' => $user->bank_account,
                ],
                code: ErrorCodes::PROFILE_UPDATED,
            );
        }

        return redirect()->back()->with('success', __('errors.'.ErrorCodes::PROFILE_UPDATED));
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('client')->user();

        $success = $this->profileService->updatePassword(
            $user,
            $request->input('current_password'),
            $request->input('new_password'),
        );

        if (! $success) {
            if ($request->expectsJson()) {
                return ApiResponse::error(
                    code: ErrorCodes::CURRENT_PASSWORD_INVALID,
                    statusCode: 400,
                );
            }

            return redirect()
                ->back()
                ->with('error', __('errors.'.ErrorCodes::CURRENT_PASSWORD_INVALID));
        }

        if ($request->expectsJson()) {
            return ApiResponse::success(code: ErrorCodes::PASSWORD_UPDATED);
        }

        return redirect()
            ->back()
            ->with('success', __('errors.'.ErrorCodes::PASSWORD_UPDATED));
    }

    public function generateDepositQr(GenerateDepositQrRequest $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('client')->user();
        $amount = $request->validated('amount');

        DepositRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        $depositData = [
            'bank_id' => config('deposit.bank_id', '970436'),
            'account_no' => config('deposit.account_no', '123456789'),
            'account_name' => config('deposit.account_name', 'NGUYEN VAN A'),
            'bank_name' => config('deposit.bank_name', 'Vietcombank'),
            'amount' => $amount,
            'content' => $user->nickname,
        ];

        if ($request->expectsJson()) {
            return ApiResponse::success(
                data: $depositData,
                code: ErrorCodes::DEPOSIT_QR_GENERATED,
            );
        }

        return redirect()
            ->back()
            ->with('deposit_data', $depositData)
            ->with('success', __('errors.'.ErrorCodes::DEPOSIT_QR_GENERATED));
    }

    public function submitKyc(SubmitKycRequest $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('client')->user();

        $result = $this->profileService->submitKyc(
            $user,
            $request->file('kyc_front'),
            $request->file('kyc_back'),
            $request->only(['full_name', 'date_of_birth', 'cccd_number']),
        );

        if ($request->expectsJson()) {
            if ($result['code'] === ErrorCodes::KYC_VERIFIED_SUCCESS) {
                return ApiResponse::success(
                    data: ['kyc_verified_at' => $user->kyc_verified_at],
                    code: ErrorCodes::KYC_VERIFIED_SUCCESS,
                    statusCode: 201,
                );
            }

            return ApiResponse::error(code: $result['code'], statusCode: 400);
        }

        if ($result['code'] === ErrorCodes::KYC_VERIFIED_SUCCESS) {
            return redirect()
                ->back()
                ->with('success', __('errors.'.ErrorCodes::KYC_VERIFIED_SUCCESS));
        }

        return redirect()
            ->back()
            ->with('error', __('errors.'.$result['code']));
    }

    public function depositHistory(Request $request): JsonResponse
    {
        $user = Auth::guard('client')->user();

        $deposits = DepositRequest::where('user_id', $user->id)
            ->latest()
            ->paginate($request->input('per_page', 10));

        return ApiResponse::success(data: $deposits);
    }

    public function submitWithdraw(SubmitWithdrawRequest $request): JsonResponse|RedirectResponse
    {
        $user = Auth::guard('client')->user();

        $result = $this->profileService->submitWithdraw(
            $user,
            (float) $request->validated('amount'),
        );

        if ($request->expectsJson()) {
            if ($result['code'] === ErrorCodes::WITHDRAW_REQUESTED) {
                return ApiResponse::success(
                    data: ['new_balance' => (float) $user->balance],
                    code: ErrorCodes::WITHDRAW_REQUESTED,
                );
            }

            return ApiResponse::error(
                code: $result['code'],
                message: __('errors.'.$result['code']),
                statusCode: 400,
            );
        }

        if ($result['code'] === ErrorCodes::WITHDRAW_REQUESTED) {
            return redirect()
                ->back()
                ->with('success', __('errors.'.ErrorCodes::WITHDRAW_REQUESTED));
        }

        return redirect()
            ->back()
            ->with('error', __('errors.'.$result['code']));
    }

    public function withdrawHistory(Request $request): JsonResponse
    {
        $user = Auth::guard('client')->user();

        $withdraws = WithdrawRequest::where('user_id', $user->id)
            ->latest()
            ->paginate($request->input('per_page', 10));

        return ApiResponse::success(data: $withdraws);
    }
}
