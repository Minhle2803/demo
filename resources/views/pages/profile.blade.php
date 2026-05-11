@extends('layouts.app')

@section('title', __('messages.profile.title'))

@section('content')

<div class="position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg profile-setting-img">
        <div class="overlay-content">
            <div class="text-end p-3">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-3">
        <div class="card mt-n5">
            <div class="card-body p-4">
                <div class="text-center">
                    <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                        <img src="{{ asset('assets/images/icons/brand.png') }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image material-shadow" alt="user-profile-image">
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                        </div>
                    </div>
                    <h5 class="fs-16 mb-1">{{ $user->nickname }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
            </div>
        </div>
        <!--end card-->

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">{{ __('messages.profile.account_info') }}</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">{{ __('messages.profile.user_id') }}</td>
                            <td class="fw-medium">{{ $user->user_id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('messages.profile.balance') }}</td>
                            <td class="fw-medium">{{ number_format($user->balance, 2) }} VND</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('messages.profile.status') }}</td>
                            <td>
                                @if ($user->isKycVerified())
                                    <span class="badge bg-success">{{ __('messages.profile.kyc_verified') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('messages.profile.not_verified') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('messages.profile.joined') }}</td>
                            <td class="fw-medium">{{ $user->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->

    <div class="col-xxl-9">
        <div class="card mt-xxl-n5">
            <div class="card-header">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'profile' ? 'active' : '' }}" data-bs-toggle="tab" href="#personalDetails" role="tab">
                            <i class="fas fa-home"></i> {{ __('messages.profile.personal_info') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}" data-bs-toggle="tab" href="#changePassword" role="tab">
                            <i class="far fa-user"></i> {{ __('messages.profile.change_password') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'deposit' ? 'active' : '' }}" data-bs-toggle="tab" href="#experience" role="tab">
                            <i class="far fa-envelope"></i> {{ __('messages.profile.deposit_withdraw') }}
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'kyc' ? 'active' : '' }}" data-bs-toggle="tab" href="#privacy" role="tab">
                            <i class="far fa-envelope"></i> {{ __('messages.profile.kyc_verification') }}
                        </a>
                    </li> -->
                </ul>
            </div>

            <div class="card-body p-4">
                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="tab-content">
                    {{-- Tab 1: Personal Info --}}
                    <div class="tab-pane {{ $activeTab === 'profile' ? 'active' : '' }}" id="personalDetails" role="tabpanel">
                        <form action="{{ route('client.profile.update') }}" method="POST" id="profileForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="userIdDisplay" class="form-label">{{ __('messages.profile.user_id') }}</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->user_id }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="nicknameInput" class="form-label">{{ __('messages.profile.nickname') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nickname" id="nicknameInput" value="{{ old('nickname', $user->nickname) }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phoneDisplay" class="form-label">{{ __('messages.profile.phone_number') }}</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->phone_number }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="balanceDisplay" class="form-label">{{ __('messages.profile.balance') }}</label>
                                        <input type="text" class="form-control bg-light" value="{{ number_format($user->balance, 2) }} VND" readonly>
                                    </div>
                                </div>
                                @if ($user->invite_code)
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.invite_link') }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="clientInviteLink" value="{{ config('app.url') }}/signup?ref={{ $user->invite_code }}" readonly>
                                            <button type="button" class="btn btn-primary" onclick="copyClientInviteLink()">
                                                {{ __('admin.copy_link') }}
                                            </button>
                                        </div>
                                        <small class="text-muted">{{ __('admin.invite_code') }}: <code>{{ $user->invite_code }}</code></small>
                                    </div>
                                </div>
                                @endif
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="kycStatus" class="form-label">KYC Status</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->isKycVerified() ? __('messages.common.verified') : ($user->kyc_front_url || $user->kyc_back_url ? __('messages.common.pending') : __('messages.common.not_submitted')) }}" readonly>
                                    </div>
                                </div>
                                @if (!$user->isKycVerified())
                                    <div class="col-12"><hr class="my-4"></div>
                                    <div class="col-12"><h5 class="mb-3">{{ __('messages.profile.kyc_modal_title') }}</h5></div>

                                    @if ($user->kyc_front_url && $user->kyc_back_url)
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                <i class="ri-information-line me-1"></i>
                                                {{ __('messages.profile.kyc_pending_msg') }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="kycBankName" class="form-label">{{ __('messages.profile.account_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kycBankName" name="account_name" value="{{ $user->account_name }}" placeholder="NGUYEN VAN A">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="kycBankNumber" class="form-label">{{ __('messages.profile.account_number') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kycBankNumber" name="bank_number" value="{{ $user->bank_number }}" placeholder="{{ __('messages.profile.enter_account_number') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="kycBankBranch" class="form-label">{{ __('messages.profile.bank_name_label') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kycBankBranch" name="bank_account" value="{{ $user->bank_account }}" placeholder="{{ __('messages.profile.enter_bank_name') }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="kycFullName" class="form-label">{{ __('messages.profile.full_name_cccd') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kycFullName" name="full_name" value="{{ $user->full_name }}" placeholder="NGUYEN VAN A">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="kycCccdNumber" class="form-label">{{ __('messages.profile.cccd_number') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kycCccdNumber" name="cccd_number" value="{{ $user->cccd_number }}" placeholder="{{ __('messages.profile.enter_cccd') }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="kycFrontInput" class="form-label">{{ __('messages.profile.cccd_front') }} <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="kyc_front" id="kycFrontInput" accept="image/jpeg,image/png,image/jpg">
                                            <div class="mt-2" id="kycFrontPreview"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="kycBackInput" class="form-label">{{ __('messages.profile.cccd_back') }} <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="kyc_back" id="kycBackInput" accept="image/jpeg,image/png,image/jpg">
                                            <div class="mt-2" id="kycBackPreview"></div>
                                        </div>
                                    </div>

                                    <div class="col-12" id="kycUploadStatus"></div>
                                @else
                                    <div class="col-12">
                                        <div class="alert alert-success d-flex align-items-center" role="alert">
                                            <i class="ri-shield-check-fill me-2" style="font-size: 24px;"></i>
                                            <div>
                                                <strong>{{ __('admin.kyc_verified') }}</strong><br>
                                                <small>{{ __('admin.kyc_verified_at') }}: {{ $user->kyc_verified_at?->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">{{ __('messages.profile.updates') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    {{-- end tab-pane --}}

                    {{-- Tab 2: Change Password --}}
                    <div class="tab-pane {{ $activeTab === 'password' ? 'active' : '' }}" id="changePassword" role="tabpanel">
                        <form action="{{ route('client.profile.password') }}" method="POST" id="passwordForm">
                            @csrf
                            <div class="row g-2">
                                <div class="col-lg-4">
                                    <div>
                                        <label for="currentPasswordInput" class="form-label">{{ __('messages.profile.current_password') }}</label>
                                        <input type="password" class="form-control" name="current_password" id="currentPasswordInput" placeholder="{{ __('messages.profile.enter_current_password') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <label for="newPasswordInput" class="form-label">{{ __('messages.profile.new_password') }}</label>
                                        <input type="password" class="form-control" name="new_password" id="newPasswordInput" placeholder="{{ __('messages.profile.enter_new_password') }}" required minlength="8">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <label for="confirmPasswordInput" class="form-label">{{ __('messages.profile.confirm_password') }}</label>
                                        <input type="password" class="form-control" name="new_password_confirmation" id="confirmPasswordInput" placeholder="{{ __('messages.profile.enter_confirm_password') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">{{ __('messages.profile.change_password') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    {{-- end tab-pane --}}

                    {{-- Tab 3: Deposit / Withdraw --}}
                    <div class="tab-pane {{ $activeTab === 'deposit' ? 'active' : '' }}" id="experience" role="tabpanel">
                        <div class="row">
                            <div class="card card-body text-center bg-light">
                                <h4 class="card-title">{{ __('messages.profile.total_assets') }}</h4>
                                <p class="card-text text-muted" id="totalBalance">{{ number_format($user->balance, 2) }}</p>
                            </div>
                        </div>

                        {{-- Sub-tabs: Deposit | Withdraw --}}
                        <ul class="nav nav-tabs nav-tabs-custom mt-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#subDeposit" role="tab">{{ __('messages.profile.deposit') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#subWithdraw" role="tab">{{ __('messages.profile.withdraw') }}</a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            {{-- Sub-tab: Deposit --}}
                            <div class="tab-pane fade show active" id="subDeposit" role="tabpanel">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-light text-primary rounded-circle fs-3 material-shadow">
                                                    <i class="ri-money-dollar-circle-fill align-middle"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0">{{ __('messages.profile.bank_name_label') }}</h4>
                                            </div>
                                            <div class="flex-shrink-0 align-self-end">
                                                <h4 class="mb-0"><span>{{ number_format($user->balance, 2) }}</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-wrap gap-3 m-3">
                                        <button type="button" class="btn btn-success waves-effect waves-light w-100" id="btn-pay" data-bs-toggle="modal" data-bs-target="#depositModal">{{ __('messages.profile.deposit') }}</button>
                                    </div>
                                </div>

                                {{-- Deposit History --}}
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('messages.profile.deposit_history') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.profile.amount_label') }}</th>
                                                        <th>{{ __('messages.common.status') }}</th>
                                                        <th>Admin Note</th>
                                                        <th>{{ __('messages.common.date') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="depositHistoryBody">
                                                    <tr><td colspan="4" class="text-center text-muted">{{ __('messages.common.loading') }}</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <nav id="depositPagination" class="d-flex justify-content-end"></nav>
                                    </div>
                                </div>
                            </div>

                            {{-- Sub-tab: Withdraw --}}
                            <div class="tab-pane fade" id="subWithdraw" role="tabpanel">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('messages.profile.withdraw_request') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="withdrawForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="withdrawAmount" class="form-label">{{ __('messages.profile.enter_withdraw_amount') }} <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="withdrawAmount" name="amount" placeholder="{{ __('messages.profile.enter_withdraw_amount') }}"  required>
                                                <p class="text-danger mt-1" id="withdrawError" style="display:none;"></p>
                                            </div>
                                            <div class="mb-3">
                                                <p class="text-muted mb-1">{{ __('messages.profile.account_holder') }}: <strong>{{ $user->account_name ?? '-' }}</strong></p>
                                                <p class="text-muted mb-1">{{ __('messages.profile.bank_name') }}: <strong>{{ $user->bank_account ?? '-' }}</strong></p>
                                                <p class="text-muted mb-1">{{ __('messages.profile.account_number') }}: <strong>{{ $user->bank_number ?? '-' }}</strong></p>
                                                @if (empty($user->account_name) || empty($user->bank_number) || empty($user->bank_account))
                                                    <p class="text-warning"><i class="ri-information-line"></i> {{ __('messages.profile.kyc_warning') }}</p>
                                                @endif
                                            </div>
                                            <button type="submit" class="btn btn-danger">{{ __('messages.profile.submit_withdraw') }}</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Withdraw History --}}
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('messages.profile.withdraw_history') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.profile.amount_label') }}</th>
                                                        <th>{{ __('messages.common.status') }}</th>
                                                        <th>Admin Note</th>
                                                        <th>{{ __('messages.common.date') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="withdrawHistoryBody">
                                                    <tr><td colspan="4" class="text-center text-muted">{{ __('messages.common.loading') }}</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <nav id="withdrawPagination" class="d-flex justify-content-end"></nav>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Deposit Modal --}}
                        <div id="depositModal" class="modal fade" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="depositModalLabel">{{ __('messages.profile.deposit') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <h5 class="fs-15">{{ __('messages.profile.enter_amount') }}</h5>
                                        <p class="text-warning"><i>Số tiền tối thiểu 300.000 VNĐ</i></p>
                                        <input type="number" class="form-control mb-3" id="depositAmount" placeholder="{{ __('messages.profile.enter_deposit_amount') }}" min="300000">
                                        <p class="mb-2 text-danger" id="depositError" style="display:none;"></p>
                                        <p class="mb-2">{{ __('messages.profile.deposit_payment_note') }}</p>
                                        <p class="mb-2"><strong>{{ __('messages.common.balance') }}: <span id="depositContent">{{ $user->nickname }}</span></strong></p>
                                        <div style="height:1px; background:linear-gradient(to right, transparent, #ccc, transparent); margin:20px 0;"></div>
                                        <p class="mb-2">{{ __('messages.profile.account_holder') }}: <strong id="accountName"></strong></p>
                                        <p class="mb-2">{{ __('messages.profile.bank_name_label') }}: <strong id="bankName"></strong></p>
                                        <p class="mb-2">{{ __('messages.profile.account_number') }}: <strong id="accountNo"></strong></p>
                                        <p class="mb-2">{{ __('messages.profile.amount_label') }}: <strong id="transferAmount"></strong> VND</p>
                                        <div style="height:1px; background:linear-gradient(to right, transparent, #ccc, transparent); margin:20px 0;"></div>
                                        <p class="text-warning"><i>{{ __('messages.profile.note_transfer') }}</i></p>
                                        <div id="qr-code"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.common.close') }}</button>
                                        <button type="button" id="depositButton" class="btn btn-success">{{ __('messages.profile.generate_qr') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end tab-pane --}}

                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->
<style>
    .profile-wid-bg::before {
        background: none !important;
    }
    .profile-setting-img {
        height: 100px !important;
    }
</style>
@endsection

@push('scripts')
@vite(['resources/js/referral.js'])
<script>
    window.profileConfig = {
        depositQrUrl: "{{ route('client.profile.deposit.qr') }}",
        depositHistoryUrl: "{{ route('client.profile.deposit.history') }}",
        withdrawSubmitUrl: "{{ route('client.profile.withdraw') }}",
        withdrawHistoryUrl: "{{ route('client.profile.withdraw.history') }}",
        updateProfileUrl: "{{ route('client.profile.update') }}",
        updatePasswordUrl: "{{ route('client.profile.password') }}",
        activeTab: "{{ $activeTab }}",
        nickname: "{{ $user->nickname }}",
    };
</script>
@endpush
