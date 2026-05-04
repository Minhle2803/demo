@extends('layouts.admin')

@section('title', __('admin.deposit_management'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.deposit_management') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.deposit_management') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table align-middle table-nowrap">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin.user') }}</th>
                                <th>{{ __('admin.nickname') }}</th>
                                <th>{{ __('admin.kyc_status') }}</th>
                                <th>{{ __('admin.bank_name') }}</th>
                                <th>{{ __('admin.amount') }}</th>
                                <th>{{ __('admin.note') }}</th>
                                <th>{{ __('admin.status') }}</th>
                                <th>{{ __('admin.time') }}</th>
                                <th>{{ __('admin.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deposits as $deposit)
                                <tr>
                                    <td>{{ $deposit->id }}</td>
                                    <td>{{ $deposit->user->user_id ?? 'N/A' }}</td>
                                    <td>{{ $deposit->user->nickname ?? 'N/A' }}</td>
                                    <td>
                                        @if ($deposit->user && $deposit->user->isKycVerified())
                                            <span class="badge bg-success-subtle text-success">{{ __('admin.kyc_verified') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ __('admin.kyc_unverified') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $deposit->user->account_name ?? 'N/A' }}</td>
                                    <td>{{ number_format((float) $deposit->amount, 2) }} VND</td>
                                    <td>{{ $deposit->admin_note ?? '-' }}</td>
                                    <td>
                                        @if ($deposit->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning">{{ __('admin.pending') }}</span>
                                        @elseif ($deposit->status === 'done')
                                            <span class="badge bg-success-subtle text-success">{{ __('admin.done') }}</span>
                                        @elseif ($deposit->status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger">{{ __('admin.rejected') }}</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info">{{ $deposit->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if ($deposit->status === 'pending')
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.deposits.approve', $deposit->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('{{ __('admin.confirm_approve_deposit') }}')">
                                                        {{ __('admin.approve') }}
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $deposit->id }}">
                                                    {{ __('admin.reject') }}
                                                </button>
                                            </div>

                                            <div class="modal fade" id="rejectModal{{ $deposit->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.deposits.reject', $deposit->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('admin.reject_deposit') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">{{ __('admin.reject_reason') }}</label>
                                                                    <textarea class="form-control" name="reason" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                                                                <button type="submit" class="btn btn-danger">{{ __('admin.confirm_reject') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $deposits->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
