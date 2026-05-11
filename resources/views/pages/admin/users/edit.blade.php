@extends('layouts.admin')

@section('title', __('admin.edit_user'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.edit_user') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('admin.user_management') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->nickname }}</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('admin.user_info') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('admin.user_id') }}</label>
                        <input type="text" class="form-control" value="{{ $user->user_id }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="nickname" class="form-label">{{ __('admin.nickname') }} <span class="text-danger">*</span></label>
                        <input type="text" name="nickname" id="nickname" class="form-control @error('nickname') is-invalid @enderror" value="{{ old('nickname', $user->nickname) }}" required>
                        @error('nickname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('admin.email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">{{ __('admin.full_name') }}</label>
                        <input type="text" name="full_name" id="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $user->full_name) }}">
                        @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">{{ __('admin.phone_number') }}</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $user->phone_number) }}">
                        @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="is_verified" class="form-label">{{ __('admin.kyc_status') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_verified" value="0">
                            <input type="checkbox" name="is_verified" id="is_verified" class="form-check-input" value="1" {{ old('is_verified', $user->verified_at ? true : false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_verified">{{ __('admin.kyc_verified') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('admin.balance') }} & {{ __('admin.bank_info') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="balance" class="form-label">{{ __('admin.balance') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="balance" id="balance" class="form-control @error('balance') is-invalid @enderror" value="{{ old('balance', $user->balance) }}" required>
                        @error('balance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="trading_balance" class="form-label">{{ __('admin.trading_balance') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="trading_balance" id="trading_balance" class="form-control @error('trading_balance') is-invalid @enderror" value="{{ old('trading_balance', $user->trading_balance) }}" required>
                        @error('trading_balance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="text-muted">

                    <div class="mb-3">
                        <label for="account_name" class="form-label">{{ __('admin.bank_account') }}</label>
                        <input type="text" name="account_name" id="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $user->account_name) }}">
                        @error('account_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bank_account" class="form-label">{{ __('admin.bank_name') }}</label>
                        <input type="text" name="bank_account" id="bank_account" class="form-control @error('bank_account') is-invalid @enderror" value="{{ old('bank_account', $user->bank_account) }}">
                        @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bank_number" class="form-label">{{ __('admin.bank_number') }}</label>
                        <input type="text" name="bank_number" id="bank_number" class="form-control @error('bank_number') is-invalid @enderror" value="{{ old('bank_number', $user->bank_number) }}">
                        @error('bank_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('admin.kyc_status') }}</h5>
                </div>
                <div class="card-body">
                    @if ($user->kyc_front_url || $user->kyc_back_url)
                        <div class="row g-3">
                            @if ($user->kyc_front_url)
                                <div class="col-lg-6">
                                    <p class="mb-1"><strong>{{ __('admin.kyc_front') }}:</strong></p>
                                    <img src="{{ asset('storage/' . $user->kyc_front_url) }}" class="img-fluid rounded border" style="max-height:250px" alt="KYC Front">
                                </div>
                            @endif
                            @if ($user->kyc_back_url)
                                <div class="col-lg-6">
                                    <p class="mb-1"><strong>{{ __('admin.kyc_back') }}:</strong></p>
                                    <img src="{{ asset('storage/' . $user->kyc_back_url) }}" class="img-fluid rounded border" style="max-height:250px" alt="KYC Back">
                                </div>
                            @endif
                        </div>
                        @if (!$user->isKycVerified())
                            <form action="{{ route('admin.users.approve-kyc', $user->id) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ri-shield-check-line align-bottom me-1"></i>{{ __('admin.kyc_approve') }}
                                </button>
                            </form>
                        @endif
                    @else
                        <p class="text-muted mb-0">{{ __('admin.no_data') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <button type="submit" class="btn btn-success">
                <i class="ri-check-line align-bottom"></i> {{ __('admin.save') }}
            </button>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-soft-secondary ms-2">
                {{ __('admin.cancel') }}
            </a>
        </div>
    </div>
</form>
@endsection
