@extends('layouts.admin')

@section('title', 'Session #' . $session->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Session #{{ $session->id }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sessions.index') }}">Sessions</a></li>
                    <li class="breadcrumb-item active">#{{ $session->id }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card" data-session-detail>
            <div class="card-header">
                <h5 class="card-title mb-0">Session Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%">Symbol</th>
                        <td>{{ $session->symbol }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Interval</th>
                        <td>{{ $session->interval }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td data-field="status">
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
                    </tr>
                    <tr>
                        <th class="text-muted">Start Time</th>
                        <td>{{ optional($session->start_time)->setTimezone('+07:00')->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Lock Time</th>
                        <td>{{ optional($session->lock_time)->setTimezone('+07:00')->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">End Time</th>
                        <td>{{ optional($session->end_time)->setTimezone('+07:00')->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Open Price</th>
                        <td>{{ $session->open_price ? number_format((float) $session->open_price, 8) : '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Close Price</th>
                        <td data-field="close_price">{{ $session->close_price ? number_format((float) $session->close_price, 8) : '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Kết quả</th>
                        <td data-field="result">
                            @if ($session->close_price && $session->open_price)
                                @if ((float) $session->close_price > (float) $session->open_price)
                                    <span class="text-success fw-bold">Mua</span>
                                @elseif ((float) $session->close_price < (float) $session->open_price)
                                    <span class="text-danger fw-bold">Bán</span>
                                @else
                                    —
                                @endif
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Candle Timestamp (ms)</th>
                        <td>{{ $session->candle_timestamp ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Timestamps</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" style="width: 40%">Created At</th>
                        <td>{{ optional($session->created_at)->setTimezone('+07:00')->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Updated At</th>
                        <td>{{ optional($session->updated_at)->setTimezone('+07:00')->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <a href="{{ route('admin.sessions.index') }}" class="btn btn-soft-secondary">
            <i class="ri-arrow-left-line align-bottom"></i> Back to Sessions
        </a>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/session-realtime.js'])
@endpush
