@extends('layouts.app')

@section('title', 'Profile')

@section('content')

<div class="position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg profile-setting-img">
        <img src="assets/images/profile-bg.jpg" class="profile-wid-img" alt="">
        <div class="overlay-content">
            <div class="text-end p-3">
                <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                    <input id="profile-foreground-img-file-input" type="file" class="profile-foreground-img-file-input">
                    <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">
                        <i class="ri-image-edit-line align-bottom me-1"></i> Change Cover
                    </label>
                </div>
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
                        <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image material-shadow" alt="user-profile-image">
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                            <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body material-shadow">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
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
                        <h5 class="card-title mb-0">Account Info</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">User ID</td>
                            <td class="fw-medium">{{ $user->user_id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Balance</td>
                            <td class="fw-medium">{{ number_format($user->balance, 2) }} VND</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if ($user->isKycVerified())
                                    <span class="badge bg-success">KYC Verified</span>
                                @else
                                    <span class="badge bg-warning">Not Verified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Joined</td>
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
                            <i class="fas fa-home"></i> Thông tin cá nhân
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}" data-bs-toggle="tab" href="#changePassword" role="tab">
                            <i class="far fa-user"></i> Change Password
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'deposit' ? 'active' : '' }}" data-bs-toggle="tab" href="#experience" role="tab">
                            <i class="far fa-envelope"></i> Nạp / Rút
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'kyc' ? 'active' : '' }}" data-bs-toggle="tab" href="#privacy" role="tab">
                            <i class="far fa-envelope"></i> KYC Verification
                        </a>
                    </li>
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
                        <form action="{{ route('client.profile.update') }}" method="POST" id="profileForm">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="userIdDisplay" class="form-label">User ID</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->user_id }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="emailDisplay" class="form-label">Email</label>
                                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="nicknameInput" class="form-label">Nick Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nickname" id="nicknameInput" value="{{ old('nickname', $user->nickname) }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phoneDisplay" class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->phone_number }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="balanceDisplay" class="form-label">Balance</label>
                                        <input type="text" class="form-control bg-light" value="{{ number_format($user->balance, 2) }} VND" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="kycStatus" class="form-label">KYC Status</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->isKycVerified() ? 'Verified' : ($user->kyc_front_url || $user->kyc_back_url ? 'Pending' : 'Not Submitted') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="bankAccountDisplay" class="form-label">Ngân Hàng</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->bank_account ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="bankNumberDisplay" class="form-label">Số tài khoản</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->bank_number ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label for="bankNameDisplay" class="form-label">Chủ tài khoản</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->account_name ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="createdAtDisplay" class="form-label">Ngày tham gia</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->created_at?->format('d/m/Y H:i') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="verifiedAtDisplay" class="form-label">Ngày xác minh</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->verified_at?->format('d/m/Y H:i') ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">Updates</button>
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
                                        <label for="currentPasswordInput" class="form-label">Current Password*</label>
                                        <input type="password" class="form-control" name="current_password" id="currentPasswordInput" placeholder="Enter current password" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <label for="newPasswordInput" class="form-label">New Password*</label>
                                        <input type="password" class="form-control" name="new_password" id="newPasswordInput" placeholder="Enter new password" required minlength="8">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <label for="confirmPasswordInput" class="form-label">Confirm Password*</label>
                                        <input type="password" class="form-control" name="new_password_confirmation" id="confirmPasswordInput" placeholder="Confirm new password" required>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">Change Password</button>
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
                                <h4 class="card-title">Tổng tài sản (VND)</h4>
                                <p class="card-text text-muted" id="totalBalance">{{ number_format($user->balance, 2) }}</p>
                            </div>
                        </div>

                        {{-- Sub-tabs: Deposit | Withdraw --}}
                        <ul class="nav nav-tabs nav-tabs-custom mt-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#subDeposit" role="tab">Nạp tiền</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#subWithdraw" role="tab">Rút tiền</a>
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
                                                <h4 class="mb-0">Ngân Hàng</h4>
                                            </div>
                                            <div class="flex-shrink-0 align-self-end">
                                                <h4 class="mb-0"><span>{{ number_format($user->balance, 2) }}</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-wrap gap-3 m-3">
                                        <button type="button" class="btn btn-success waves-effect waves-light w-100" id="btn-pay" data-bs-toggle="modal" data-bs-target="#depositModal">Nạp tiền</button>
                                    </div>
                                </div>

                                {{-- Deposit History --}}
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Lịch sử nạp tiền</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Admin Note</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="depositHistoryBody">
                                                    <tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>
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
                                        <h5 class="card-title mb-0">Yêu cầu rút tiền</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="withdrawForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="withdrawAmount" class="form-label">Số tiền rút (tối thiểu 10,000 VND) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="withdrawAmount" name="amount" placeholder="Nhập số tiền cần rút" min="10000" required>
                                                <p class="text-danger mt-1" id="withdrawError" style="display:none;"></p>
                                            </div>
                                            <div class="mb-3">
                                                <p class="text-muted mb-1">Tài khoản nhận: <strong>{{ $user->account_name ?? '-' }}</strong></p>
                                                <p class="text-muted mb-1">Ngân hàng: <strong>{{ $user->bank_account ?? '-' }}</strong></p>
                                                <p class="text-muted mb-1">Số tài khoản: <strong>{{ $user->bank_number ?? '-' }}</strong></p>
                                                @if (empty($user->account_name) || empty($user->bank_number) || empty($user->bank_account))
                                                    <p class="text-warning"><i class="ri-information-line"></i> Vui lòng cập nhật thông tin ngân hàng trong modal KYC trước khi rút tiền.</p>
                                                @endif
                                            </div>
                                            <button type="submit" class="btn btn-danger">Gửi yêu cầu rút tiền</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Withdraw History --}}
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Lịch sử rút tiền</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Admin Note</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="withdrawHistoryBody">
                                                    <tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>
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
                                        <h5 class="modal-title" id="depositModalLabel">Nạp tiền</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <h5 class="fs-15">Nhập số tiền</h5>
                                        <input type="number" class="form-control mb-3" id="depositAmount" placeholder="Nhập số tiền Nạp" min="10000">
                                        <p class="mb-2 text-danger" id="depositError" style="display:none;"></p>
                                        <p class="mb-2">Để thanh toán bạn vui lòng chuyển tiền theo</p>
                                        <p class="mb-2"><strong>Nội dung: <span id="depositContent">{{ $user->nickname }}</span></strong></p>
                                        <div style="height:1px; background:linear-gradient(to right, transparent, #ccc, transparent); margin:20px 0;"></div>
                                        <p class="mb-2">Chủ tài khoản: <strong id="accountName"></strong></p>
                                        <p class="mb-2">Ngân hàng: <strong id="bankName"></strong></p>
                                        <p class="mb-2">Số tài khoản: <strong id="accountNo"></strong></p>
                                        <p class="mb-2">Số tiền: <strong id="transferAmount"></strong> VND</p>
                                        <div style="height:1px; background:linear-gradient(to right, transparent, #ccc, transparent); margin:20px 0;"></div>
                                        <p class="text-warning"><i>Lưu ý: Hệ thống sẽ không chịu trách nhiệm nếu bạn gửi sai nội dung</i></p>
                                        <div id="qr-code"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="depositButton" class="btn btn-success">Tạo mã QR</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end tab-pane --}}

                    {{-- Tab 4: KYC Verification --}}
                    <div class="tab-pane {{ $activeTab === 'kyc' ? 'active' : '' }}" id="privacy" role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                @if ($user->isKycVerified())
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="mb-4">
                                                <lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon>
                                            </div>
                                            <h4 class="fw-semibold">KYC Verified</h4>
                                            <p class="text-muted">Your account has been verified on {{ $user->kyc_verified_at?->format('d/m/Y H:i') }}.</p>
                                            <div class="mt-3">
                                                @if ($user->kyc_front_url)
                                                    <a href="{{ Storage::url($user->kyc_front_url) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">View Front ID</a>
                                                @endif
                                                @if ($user->kyc_back_url)
                                                    <a href="{{ Storage::url($user->kyc_back_url) }}" target="_blank" class="btn btn-sm btn-outline-primary">View Back ID</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="mb-4">
                                                <i class="ri-shield-check-fill text-muted" style="font-size: 80px;"></i>
                                            </div>
                                            <h4 class="fw-semibold">KYC Verification</h4>
                                            <p class="text-muted">Xác minh danh tính của bạn để sử dụng đầy đủ tính năng.</p>
                                            @if ($user->kyc_front_url && !$user->isKycVerified())
                                                <div class="alert alert-warning mt-3">
                                                    <i class="ri-information-line me-1"></i>
                                                    Your KYC documents have been submitted and are pending verification.
                                                </div>
                                            @endif
                                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#kycModal">
                                                <i class="ri-user-shared-line me-1"></i> Verify KYC
                                            </button>
                                        </div>
                                    </div>
                                @endif
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
@endsection

{{-- KYC 2-Step Modal --}}
<div id="kycModal" class="modal fade" tabindex="-1" aria-labelledby="kycModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kycModalLabel">KYC Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Step indicators --}}
                <div class="d-flex justify-content-center mb-4" id="kycSteps">
                    <span class="badge bg-primary fs-6 px-3 py-2 me-2" id="kycStep1Badge">Step 1: Bank Info</span>
                    <span class="text-muted fs-5">→</span>
                    <span class="badge bg-light text-dark fs-6 px-3 py-2 ms-2" id="kycStep2Badge">Step 2: Upload CCCD</span>
                </div>

                {{-- Step 1: Bank Info --}}
                <div id="kycStep1Content">
                    <form id="kycBankForm">
                        @csrf
                        <div class="mb-3">
                            <label for="kycBankName" class="form-label">Tên chủ tài khoản <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kycBankName" name="account_name" value="{{ $user->account_name }}" placeholder="NGUYEN VAN A" required>
                        </div>
                        <div class="mb-3">
                            <label for="kycBankNumber" class="form-label">Số tài khoản <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kycBankNumber" name="bank_number" value="{{ $user->bank_number }}" placeholder="Nhập số tài khoản" required>
                        </div>
                        <div class="mb-3">
                            <label for="kycBankBranch" class="form-label">Ngân hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kycBankBranch" name="bank_account" value="{{ $user->bank_account }}" placeholder="VD: Vietcombank, BIDV..." required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="kycNextStep">Tiếp theo</button>
                        </div>
                    </form>
                </div>

                {{-- Step 2: Upload CCCD --}}
                <div id="kycStep2Content" style="display:none;">
                    <form id="kycUploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="kycFullName" class="form-label">Họ và tên (trên CCCD) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kycFullName" name="full_name" value="{{ $user->full_name }}" placeholder="NGUYEN VAN A" required>
                        </div>
                        <div class="mb-3">
                            <label for="kycDob" class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="kycDob" name="date_of_birth" value="{{ $user->date_of_birth?->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="kycCccdNumber" class="form-label">Số CCCD <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kycCccdNumber" name="cccd_number" value="{{ $user->cccd_number }}" placeholder="Nhập số CCCD" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="kycFrontInput" class="form-label">Mặt trước CCCD <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="kyc_front" id="kycFrontInput" accept="image/jpeg,image/png,image/jpg" required>
                                <div class="mt-2" id="kycFrontPreview"></div>
                            </div>
                            <div class="col-lg-6">
                                <label for="kycBackInput" class="form-label">Mặt sau CCCD <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="kyc_back" id="kycBackInput" accept="image/jpeg,image/png,image/jpg" required>
                                <div class="mt-2" id="kycBackPreview"></div>
                            </div>
                        </div>
                        <div class="mt-3" id="kycUploadStatus"></div>
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-light" id="kycPrevStep">Quay lại</button>
                            <button type="submit" class="btn btn-primary">Gửi xác minh</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.profileConfig = {
        depositQrUrl: "{{ route('client.profile.deposit.qr') }}",
        depositHistoryUrl: "{{ route('client.profile.deposit.history') }}",
        withdrawSubmitUrl: "{{ route('client.profile.withdraw') }}",
        withdrawHistoryUrl: "{{ route('client.profile.withdraw.history') }}",
        kycSubmitUrl: "{{ route('client.profile.kyc') }}",
        updateProfileUrl: "{{ route('client.profile.update') }}",
        updatePasswordUrl: "{{ route('client.profile.password') }}",
        updateBankUrl: "{{ route('client.profile.bank') }}",
        activeTab: "{{ $activeTab }}",
        nickname: "{{ $user->nickname }}",
    };
</script>
@endpush
