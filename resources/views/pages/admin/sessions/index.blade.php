@extends('layouts.admin')

@section('title', 'Session Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Session Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item active">Sessions</li>
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
                    <div class="col-auto">
                        <form method="GET" action="{{ route('admin.sessions.index') }}" class="d-flex gap-2 flex-wrap">
                            <select class="form-select form-select-sm w-auto" name="symbol" onchange="this.form.submit()">
                                <option value="">All Symbols</option>
                                @foreach ($symbols as $sym)
                                    <option value="{{ $sym }}" {{ request('symbol') === $sym ? 'selected' : '' }}>{{ $sym }}</option>
                                @endforeach
                            </select>

                            <select class="form-select form-select-sm w-auto" name="status" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="future" {{ request('status') === 'future' ? 'selected' : '' }}>Future</option>
                                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Locked</option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>

                            <select class="form-select form-select-sm w-auto" name="session_type" onchange="this.form.submit()">
                                <option value="future" {{ request('session_type', 'future') === 'future' ? 'selected' : '' }}>Future Sessions</option>
                                <option value="realtime" {{ request('session_type') === 'realtime' ? 'selected' : '' }}>Realtime Sessions</option>
                                <option value="all" {{ request('session_type') === 'all' ? 'selected' : '' }}>All</option>
                            </select>

                            <input type="date" class="form-control form-control-sm w-auto" name="from" value="{{ request('from') }}" onchange="this.form.submit()" placeholder="From">

                            <input type="date" class="form-control form-control-sm w-auto" name="to" value="{{ request('to') }}" onchange="this.form.submit()" placeholder="To">
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
                                <th>Symbol</th>
                                <th>Interval</th>
                                <th>Status</th>
                                <th>Start Time</th>
                                <th>Lock Time</th>
                                <th>End Time</th>
                                <th>Open Price</th>
                                <th>Close Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sessions as $session)
                                <tr>
                                    <td>{{ $session->id }}</td>
                                    <td>{{ $session->symbol }}</td>
                                    <td>{{ $session->interval }}</td>
                                    <td>
                                        @if ($session->status === 'future')
                                            <span class="badge bg-info-subtle text-info">Future</span>
                                        @elseif ($session->status === 'open')
                                            <span class="badge bg-success-subtle text-success">Open</span>
                                        @elseif ($session->status === 'locked')
                                            <span class="badge bg-warning-subtle text-warning">Locked</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Closed</span>
                                        @endif
                                    </td>
                                    <td>{{ $session->start_time }}</td>
                                    <td>{{ $session->lock_time }}</td>
                                    <td>{{ $session->end_time }}</td>
                                    <td>{{ $session->open_price ? number_format((float) $session->open_price, 8) : '—' }}</td>
                                    <td>{{ $session->close_price ? number_format((float) $session->close_price, 8) : '—' }}</td>
                                    <td>
                                        <a href="{{ route('admin.sessions.show', $session->id) }}" class="btn btn-sm btn-soft-info">
                                            <i class="ri-eye-line align-bottom"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted py-3">No sessions found. The workers will generate sessions automatically.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $sessions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
