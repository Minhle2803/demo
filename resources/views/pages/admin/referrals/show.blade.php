@extends('layouts.admin')

@section('title', 'Agent #' . $agent->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Agent: {{ $agent->nickname }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.referrals.index') }}">Referrals</a></li>
                    <li class="breadcrumb-item active">{{ $agent->nickname }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.user_info') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%">{{ __('admin.user_id') }}</th>
                        <td>{{ $agent->id }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('admin.nickname') }}</th>
                        <td>{{ $agent->nickname }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('admin.email') }}</th>
                        <td>{{ $agent->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('admin.phone_number') }}</th>
                        <td>{{ $agent->phone_number ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('admin.invite_code') }}</th>
                        <td><code>{{ $agent->invite_code ?? '—' }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('admin.invited_by') }}</th>
                        <td>{{ $agent->invitedByClient->nickname ?? 'Admin' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.agent_stats') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%">{{ __('admin.total_played_amount') }}</th>
                        <td><strong>{{ number_format($stats['total_played']) }} VND</strong></td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('admin.total_won_amount') }}</th>
                        <td><strong>{{ number_format($stats['total_won']) }} VND</strong></td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('admin.total_trading_fees') }}</th>
                        <td><strong>{{ number_format($stats['total_fees']) }} VND</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">{{ __('admin.invited_users_by_agent') }} ({{ $invitedUsers->total() }})</h5>
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
                                <th>{{ __('admin.total_played_amount') }}</th>
                                <th>{{ __('admin.total_won_amount') }}</th>
                                <th>{{ __('admin.total_trading_fees') }}</th>
                                <th>{{ __('admin.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invitedUsers as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->nickname }}</td>
                                    <td>{{ $user->email ?? '—' }}</td>
                                    <td>{{ $user->phone_number ?? '—' }}</td>
                                    <td>{{ number_format((float) $user->total_played, 0, '.', ',') }}</td>
                                    <td>{{ number_format((float) $user->total_won, 0, '.', ',') }}</td>
                                    <td>{{ number_format((float) $user->total_fees, 0, '.', ',') }}</td>
                                    <td>{{ optional($user->created_at)->setTimezone('+07:00')->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
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

<div class="row mt-2">
    <div class="col-12">
        <a href="{{ route('admin.referrals.index') }}" class="btn btn-soft-secondary">
            <i class="ri-arrow-left-line align-bottom"></i> Back to Referrals
        </a>
    </div>
</div>
@endsection
