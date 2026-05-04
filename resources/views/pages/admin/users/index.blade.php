@extends('layouts.admin')

@section('title', __('admin.user_management'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.user_management') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.user_management') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm-3">
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            <div class="search-box">
                                <input type="text" class="form-control search" name="search" placeholder="{{ __('admin.search_users') }}" value="{{ request('search') }}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-auto ms-auto">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                            <select class="form-select form-select-sm w-auto" name="kyc" onchange="this.form.submit()">
                                <option value="">{{ __('admin.all_kyc_status') }}</option>
                                <option value="verified" {{ request('kyc') === 'verified' ? 'selected' : '' }}>{{ __('admin.kyc_verified') }}</option>
                                <option value="unverified" {{ request('kyc') === 'unverified' ? 'selected' : '' }}>{{ __('admin.kyc_unverified') }}</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table align-middle table-nowrap">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin.user_id') }}</th>
                                <th>{{ __('admin.nickname') }}</th>
                                <th>{{ __('admin.email') }}</th>
                                <th>{{ __('admin.phone_number') }}</th>
                                <th>{{ __('admin.balance') }}</th>
                                <th>{{ __('admin.kyc_status') }}</th>
                                <th>{{ __('admin.created_at') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->user_id }}</td>
                                    <td>{{ $user->nickname }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td>{{ number_format((float) $user->balance, 2) }}</td>
                                    <td>
                                        @if ($user->isKycVerified())
                                            <span class="badge bg-success-subtle text-success">{{ __('admin.kyc_verified') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ __('admin.kyc_unverified') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-soft-info">
                                            <i class="ri-eye-line align-bottom"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
