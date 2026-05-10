@extends('layouts.admin')

@section('title', 'Referrals')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Referrals</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item active">Referrals</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if ($inviteLink)
<div class="row mb-3">
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
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-light text-primary rounded-circle fs-3 material-shadow">
                            <i class="ri-money-dollar-circle-fill align-middle"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{ __('admin.total_played_amount') }}</p>
                        <h4 class="mb-0">{{ number_format($stats['total_played']) }} VND</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-light text-success rounded-circle fs-3 material-shadow">
                            <i class="ri-trophy-fill align-middle"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{ __('admin.total_won_amount') }}</p>
                        <h4 class="mb-0">{{ number_format($stats['total_won']) }} VND</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-light text-secondary rounded-circle fs-3 material-shadow">
                            <i class="ri-percent-fill align-middle"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{ __('admin.total_trading_fees') }}</p>
                        <h4 class="mb-0">{{ number_format($stats['total_fees']) }} VND</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">{{ __('admin.invited_members') }} ({{ $invitedUsers->total() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table align-middle table-nowrap">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin.nickname') }}</th>
                                <th>{{ __('admin.email') }}</th>
                                <th>{{ __('admin.phone_number') }}</th>
                                <th>{{ __('admin.invite_code') }}</th>
                                <th>{{ __('admin.total_played_amount') }}</th>
                                <th>{{ __('admin.total_won_amount') }}</th>
                                <th>{{ __('admin.total_trading_fees') }}</th>
                                <th>{{ __('admin.created_at') }}</th>
                                <th>{{ __('admin.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invitedUsers as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->nickname }}</td>
                                    <td>{{ $user->email ?? '—' }}</td>
                                    <td>{{ $user->phone_number ?? '—' }}</td>
                                    <td><code>{{ $user->invite_code ?? '—' }}</code></td>
                                    <td>{{ number_format((float) $user->total_played, 0, '.', ',') }}</td>
                                    <td>{{ number_format((float) $user->total_won, 0, '.', ',') }}</td>
                                    <td>{{ number_format((float) $user->total_fees, 0, '.', ',') }}</td>
                                    <td>{{ optional($user->created_at)->setTimezone('+07:00')->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.referrals.show', $user->id) }}" class="btn btn-sm btn-soft-primary">
                                            {{ __('admin.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $invitedUsers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/referral.js'])
@endpush
