@extends('layouts.admin')

@section('title', __('admin.kyc_verification'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.kyc_verification') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.kyc_verification') }}</li>
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
                        <form method="GET" action="{{ route('admin.users.kyc') }}">
                            <div class="search-box">
                                <input type="text" class="form-control search" name="search" placeholder="{{ __('admin.search_users') }}" value="{{ request('search') }}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
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
                                    <td>
                                        <span class="badge bg-warning-subtle text-warning">{{ __('admin.kyc_pending_approval') }}</span>
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-soft-info">
                                                <i class="ri-eye-line align-bottom"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-soft-warning">
                                                <i class="ri-edit-line align-bottom"></i>
                                            </a>
                                            <form action="{{ route('admin.users.approve-kyc', $user->id) }}" method="POST" onsubmit="return confirm('{{ __('admin.kyc_approve') }}?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-soft-success">
                                                    <i class="ri-shield-check-line align-bottom"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
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
