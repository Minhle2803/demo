@extends('layouts.admin')

@section('title', __('admin.settings'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.settings') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.settings') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if ($inviteLink)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ __('admin.invite_link') }}</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" id="inviteLinkInput" value="{{ $inviteLink }}" readonly>
                            <button type="button" class="btn btn-primary" onclick="copyInviteLink()">
                                {{ __('admin.copy_link') }}
                            </button>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">{{ __('admin.invite_code') }}:</small>
                        <h5 class="mb-0">{{ $admin->invite_code }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.bank_settings') }}</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form action="{{ route('admin.settings.bank') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.bank_name') }}</label>
                        <select class="form-control" name="bank_name">
                            <option value="">{{ __('messages.profile.select_bank') }}</option>
                            @foreach ($bank_list as $bank)
                                <option value="{{ $bank['code'] }}" {{ old('bank_name', $bankInfo['bank_name'] ?? '') === $bank['code'] || old('bank_name', $bankInfo['bank_name'] ?? '') === $bank['name'] ? 'selected' : '' }}>
                                    {{ $bank['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.bank_account') }}</label>
                        <input type="text" class="form-control" name="bank_account" value="{{ $bankInfo['bank_account'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.bank_number') }}</label>
                        <input type="text" class="form-control" name="bank_number" value="{{ $bankInfo['bank_number'] ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.trading_fee_setting') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.fee') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.trading_fee_percent') }}</label>
                        <input type="number" class="form-control" name="fee_percent" value="{{ $feePercent }}" step="0.01" min="0" max="100">
                        @error('fee_percent') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">{{ __('admin.trading_fee_desc') }}</small>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.min_deposit_setting') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.min-deposit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.min_deposit_label') }}</label>
                        <input type="number" class="form-control" name="min_deposit" value="{{ $minDeposit }}" step="10000" min="10000" max="100000000">
                        @error('min_deposit') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">{{ __('admin.min_deposit_desc') }}</small>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.logo_setting') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="text-muted">{{ __('admin.current_logo') }}:</p>
                    <img src="{{ $logo }}" alt="Logo" style="max-height:60px" class="mb-2">
                </div>
                <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.upload_new_logo') }}</label>
                        <input type="file" class="form-control" name="logo" accept="image/*">
                        @error('logo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('admin.upload') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/referral.js'])
@endpush
