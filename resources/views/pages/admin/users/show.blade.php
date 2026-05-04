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
                    <tr><th class="ps-0">{{ __('admin.created_at') }}</th><td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td></tr>
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
                    <tr><th class="ps-0">{{ __('admin.bank_account') }}</th><td>{{ $user->bank_account ?? 'N/A' }}</td></tr>
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
                    @if ($user->kyc_front_url)
                        <p class="mt-2"><strong>{{ __('admin.kyc_front') }}:</strong></p>
                        <img src="{{ asset('storage/' . $user->kyc_front_url) }}" class="img-fluid rounded" style="max-height:200px" alt="KYC Front">
                    @endif
                    @if ($user->kyc_back_url)
                        <p class="mt-2"><strong>{{ __('admin.kyc_back') }}:</strong></p>
                        <img src="{{ asset('storage/' . $user->kyc_back_url) }}" class="img-fluid rounded" style="max-height:200px" alt="KYC Back">
                    @endif
                @else
                    <span class="badge bg-danger-subtle text-danger fs-13">{{ __('admin.kyc_unverified') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
