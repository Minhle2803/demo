@extends('layouts.admin')

@section('title', __('admin.user_detail'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.user_detail') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('admin.user_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ $user->nickname }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
            <i class="ri-edit-line align-bottom"></i> {{ __('admin.edit_user') }}
        </a>
        <a href="{{ route('admin.users.change-password', $user->id) }}" class="btn btn-warning">
            <i class="ri-lock-password-line"></i> Đổi mật khẩu
        </a>
        @if ($user->is_blocked)
            <a href="javascript:void(0)" class="btn btn-success" id="unblockUserBtn">
                <i class="ri-checkbox-circle-line"></i> Bỏ chặn tài khoản
            </a>
        @else
            <a href="javascript:void(0)" class="btn btn-danger" id="blockUserBtn">
                <i class="ri-lock-line"></i> Chặn tài khoản
            </a>
        @endif
        <a href="{{ route('admin.users.index') }}" class="btn btn-soft-secondary ms-2">
            <i class="ri-arrow-left-line align-bottom"></i> {{ __('admin.back_to_site') }}
        </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.user_info') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr><th class="ps-0">{{ __('admin.user_id') }}</th><td>{{ $user->user_id }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.nickname') }}</th><td>{{ $user->nickname }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.email') }}</th><td>{{ $user->email }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.phone_number') }}</th><td>{{ $user->phone_number ?? 'N/A' }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.full_name') }}</th><td>{{ $user->full_name ?? 'N/A' }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.balance') }}</th><td>{{ number_format((float) $user->balance, 2) }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.trading_balance') }}</th><td>{{ number_format((float) $user->trading_balance, 2) }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.created_at') }}</th><td>{{ $user->created_at->timezone('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.bank_info') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr><th class="ps-0">{{ __('admin.bank_name') }}</th><td>{{ $user->account_name ?? 'N/A' }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.bank_account') }}</th><td>{{ $userBankName ?? ($user->bank_account ?? 'N/A') }}</td></tr>
                    <tr><th class="ps-0">{{ __('admin.bank_number') }}</th><td>{{ $user->bank_number ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.kyc_status') }}</h5>
            </div>
            <div class="card-body">
                @if ($user->isKycVerified())
                    <span class="badge bg-success-subtle text-success fs-13">{{ __('admin.kyc_verified') }}</span>
                    <p class="mt-2 text-muted">{{ __('admin.kyc_verified_at') }}: {{ $user->kyc_verified_at?->format('Y-m-d H:i:s') }}</p>
                @elseif ($user->kyc_front_url && $user->kyc_back_url)
                    <span class="badge bg-warning-subtle text-warning fs-13">{{ __('admin.kyc_pending_approval') }}</span>
                    <form action="{{ route('admin.users.approve-kyc', $user->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="ri-shield-check-line align-bottom me-1"></i>{{ __('admin.kyc_approve') }}
                        </button>
                    </form>
                @else
                    <span class="badge bg-danger-subtle text-danger fs-13">{{ __('admin.kyc_unverified') }}</span>
                @endif

                @if ($user->kyc_front_url)
                    <p class="mt-2"><strong>{{ __('admin.kyc_front') }}:</strong></p>
                    <img src="{{ asset('storage/' . $user->kyc_front_url) }}" class="img-fluid rounded" style="max-height:200px" alt="KYC Front">
                @endif
                @if ($user->kyc_back_url)
                    <p class="mt-2"><strong>{{ __('admin.kyc_back') }}:</strong></p>
                    <img src="{{ asset('storage/' . $user->kyc_back_url) }}" class="img-fluid rounded" style="max-height:200px" alt="KYC Back">
                @endif
            </div>
        </div>
    </div>
    
</div>
<div class="row">
    <div class="col-xxl-12">
    <div class="table-responsive ">
        <table class="table align-middle table-nowrap"  id="tradesTable">
            <thead class="table-light text-muted">
                <tr>
                    <th>{{ __('messages.trading.symbol') }}</th>
                    <th>{{ __('messages.trading.session_id') }}</th>
                    <th>{{ __('messages.trading.type') }}</th>
                    <th>{{ __('messages.trading.open_price') }}</th>
                    <th>{{ __('messages.trading.close_price') }}</th>
                    <th>{{ __('messages.trading.status') }}</th>
                    <th>{{ __('messages.trading.amount') }}</th>
                    <th>{{ __('messages.trading.trading_fee') }}</th>
                    <th>{{ __('messages.trading.payout') }}</th>
                    <th>{{ __('messages.trading.time') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trades as $trade)
                    <tr data-id="{{ $trade->id }}">
                        <td>
                            <?php
                                // Map session_symbol to a human-readable name and icon
                                $coinMeta = config('trading_chart.coins', []);
                                $tradeSymbol = $trade->session_symbol;
                                $meta = $coinMeta[$tradeSymbol] ?? [];
                                $displayName = $meta['name'] ?? $tradeSymbol;
                                $iconPath = asset('assets/images/svg/crypto-icons/' . ($meta['icon'] ?? 'default').'.svg');
                            ?>
                            <div class="d-flex align-items-center fw-medium">
                                <img src="{{ $iconPath }}" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">{{ $displayName }}</a>
                            </div>
                        </td>
                        <td>{{ $trade->session_id }}</td>
                        <td>
                            <?php
                                $textClass = "text-success";
                                if ($trade->type === 'sell') {
                                    $textClass = "text-danger";
                                }
                                $type = "MUA";
                                if ($trade->type === 'sell') {
                                    $type = "BÁN";
                                }
                            ?>
                            <h6 class="{{ $textClass }} fs-13 mb-0">{{ $type }}</h6>
                        </td>
                        <td>{{ $trade->session_open_price }}</td>
                        <td>{{ $trade->session_close_price }}</td>
                        <td>
                            <?php
                                $textClass = "text-success";
                                if ($trade->status === 'lose') {
                                    $textClass = "text-danger";
                                }
                                $status = "THẮNG";
                                if ($trade->status === 'lose') {
                                    $status = "THUA";
                                } else if ($trade->status === 'pending') {
                                    $status = "ĐANG CHỜ";
                                }
                            ?>
                            <h6 class="{{ $textClass }} fs-13 mb-0">{{ $status }}</h6>    
                        </td>
                        <td>{{ number_format($trade->amount, 0, '.', ',') }}</td>
                        <td>{{ $trade->trading_fee ? number_format((float) $trade->trading_fee, 0, '.', ',') : '—' }}</td>
                        <?php
                            $textClass = "text-success";
                            $text = "";
                            if ($trade->status === 'lose') {
                                $textClass = "text-danger";
                                $text = "-". number_format($trade->amount, 0, '.', ',');
                            } else if ($trade->status === 'win') {
                                $text = "+". number_format($trade->payout, 0, '.', ',');
                            }
                        ?>
                        <td>
                            <h6 class="{{ $textClass }} fs-13 mb-0">{{ $text }}</h6>
                        </td>
                        <td>{{ $trade->created_at->timezone('Asia/Ho_Chi_Minh') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mt-3">
        <div class="pagination-wrap hstack gap-2">
            <?php
                // Preserve existing query parameters except 'page'
                $disabledPrev = $trades->onFirstPage() ? 'disabled' : '';
                $hrefPrev = $trades->onFirstPage() ? '#' : $trades->previousPageUrl();

                $disabledNext = $trades->hasMorePages() ? '' : 'disabled';
                $hrefNext = $trades->hasMorePages() ? $trades->nextPageUrl() : '#';
            ?>
            <a class="page-item pagination-prev {{ $disabledPrev }}" href="{{ $hrefPrev }}">
                {{ __('messages.common.previous') }}
            </a>
            <ul class="pagination listjs-pagination mb-0"></ul>
            <a class="page-item pagination-next {{ $disabledNext }}" href="{{ $hrefNext }}">
                {{ __('messages.common.next') }}
            </a>
        </div>
    </div>
    </div>
</div>

<!-- Trade Confirmation Modal -->
<div class="modal fade" id="blockConfirmModal" tabindex="-1" aria-labelledby="blockConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blockConfirmModalLabel">Khóa tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <h4 id="confirm-type" class="fw-bold"></h4>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-1">Bạn chắc chắn muốn khóa tài khoản {{ $user->nickname }}?</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                <form method="POST" action="{{ route('admin.users.block', $user->id) }}">
                    @csrf

                    <button type="submit" class="btn btn-success">
                        {{ __('messages.common.confirm') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Trade Confirmation Modal -->
<div class="modal fade" id="unBlockConfirmModal" tabindex="-1" aria-labelledby="unBlockConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unBlockConfirmModalLabel">Mở khóa tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <h4 id="confirm-type" class="fw-bold"></h4>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-1">Bạn chắc chắn muốn mở khóa tài khoản {{ $user->nickname }}?</p>
                    <h5 id="confirm-symbol"></h5>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                <form method="POST" action="{{ route('admin.users.unblock', $user->id) }}">
                    @csrf

                    <button type="submit" class="btn btn-success">
                        {{ __('messages.common.confirm') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
 
    <script>
        function showBlockConfirm() {
            const modal = new bootstrap.Modal(document.getElementById('blockConfirmModal'));
            modal.show();
        }

        function showUnBlockConfirm() {
            const modal = new bootstrap.Modal(document.getElementById('unBlockConfirmModal'));
            modal.show();
        }

        document.getElementById('blockUserBtn')?.addEventListener('click', () => showBlockConfirm());
        document.getElementById('unblockUserBtn')?.addEventListener('click', () => showUnBlockConfirm());
    </script>
@endpush