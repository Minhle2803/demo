@extends('layouts.admin')

@section('title', __('admin.dashboard'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.dashboard') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.dashboard') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xxl-12 order-xxl-0 order-first">
        <div class="d-flex flex-column h-100">
            <div class="row h-100">
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-light text-success rounded-circle fs-3 material-shadow">
                                        <i class="ri-arrow-up-circle-fill align-middle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{ __('admin.total_spot_buy_orders') }}</p>
                                    <h4 class="mb-0">{{ number_format($stats['total_spot_buy_orders']) }}</h4>
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
                                    <span class="avatar-title bg-light text-danger rounded-circle fs-3 material-shadow">
                                        <i class="ri-arrow-down-circle-fill align-middle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{ __('admin.total_spot_sell_orders') }}</p>
                                    <h4 class="mb-0">{{ number_format($stats['total_spot_sell_orders']) }}</h4>
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
                                    <span class="avatar-title bg-light text-info rounded-circle fs-3 material-shadow">
                                        <i class="ri-exchange-dollar-fill align-middle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{ __('admin.revenue') }}</p>
                                    <h4 class="mb-0">{{ number_format($stats['revenue']) }} VND</h4>
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
                                    <span class="avatar-title bg-light text-primary rounded-circle fs-3 material-shadow">
                                        <i class="ri-bar-chart-fill align-middle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{ __('admin.total_trading_buy_orders') }}</p>
                                    <h4 class="mb-0">{{ number_format($stats['total_trading_buy_orders']) }}</h4>
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
                                    <span class="avatar-title bg-light text-warning rounded-circle fs-3 material-shadow">
                                        <i class="ri-line-chart-fill align-middle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">{{ __('admin.total_trading_sell_orders') }}</p>
                                    <h4 class="mb-0">{{ number_format($stats['total_trading_sell_orders']) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">{{ __('admin.market_graph') }}</h4>
                            <div class="flex-shrink-0">
                                <select class="form-select form-select-sm" id="timelineToggle">
                                    <option value="realtime">Realtime</option>
                                    <option value="future">Future (+10 sessions)</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body p-0 pb-3">
                            <div id="chart_container" style="width: 100%; height: 500px; min-height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-xxl-6 col-lg-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ __('admin.recent_spot_buy_orders') }}</h4>
                <div class="flex-shrink-0">
                    <select class="form-select form-select-sm" id="buySymbolFilter" onchange="window.location.search='?buy_symbol='+this.value">
                        <option value="">{{ __('admin.all_symbols') }}</option>
                        @foreach ($symbols as $sym)
                            <option value="{{ $sym }}" {{ request('buy_symbol') === $sym ? 'selected' : '' }}>{{ $sym }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-nowrap align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>{{ __('admin.user') }}</th>
                                <th>{{ __('admin.symbol') }}</th>
                                <th>{{ __('admin.price') }}</th>
                                <th>{{ __('admin.quantity') }}</th>
                                <th>{{ __('admin.total') }}</th>
                                <th>{{ __('admin.status') }}</th>
                                <th>{{ __('admin.time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentBuyOrders as $order)
                                <tr>
                                    <td>{{ $order['user']['nickname'] ?? 'N/A' }}</td>
                                    <td>{{ $order['symbol'] }}</td>
                                    <td>{{ number_format((float) $order['price'], 8) }}</td>
                                    <td>{{ number_format((float) $order['quantity'], 8) }}</td>
                                    <td>{{ number_format((float) $order['total_amount'], 8) }}</td>
                                    <td>
                                        @if ($order['status'] === 'filled')
                                            <span class="badge bg-success-subtle text-success">{{ __('admin.filled') }}</span>
                                        @elseif ($order['status'] === 'open')
                                            <span class="badge bg-info-subtle text-info">{{ __('admin.open') }}</span>
                                        @elseif ($order['status'] === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger">{{ __('admin.cancelled') }}</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">{{ $order['status'] }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order['created_at'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-6 col-lg-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ __('admin.recent_spot_sell_orders') }}</h4>
                <div class="flex-shrink-0">
                    <select class="form-select form-select-sm" id="sellSymbolFilter" onchange="window.location.search='?sell_symbol='+this.value">
                        <option value="">{{ __('admin.all_symbols') }}</option>
                        @foreach ($symbols as $sym)
                            <option value="{{ $sym }}" {{ request('sell_symbol') === $sym ? 'selected' : '' }}>{{ $sym }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-nowrap align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>{{ __('admin.user') }}</th>
                                <th>{{ __('admin.symbol') }}</th>
                                <th>{{ __('admin.price') }}</th>
                                <th>{{ __('admin.quantity') }}</th>
                                <th>{{ __('admin.total') }}</th>
                                <th>{{ __('admin.status') }}</th>
                                <th>{{ __('admin.time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentSellOrders as $order)
                                <tr>
                                    <td>{{ $order['user']['nickname'] ?? 'N/A' }}</td>
                                    <td>{{ $order['symbol'] }}</td>
                                    <td>{{ number_format((float) $order['price'], 8) }}</td>
                                    <td>{{ number_format((float) $order['quantity'], 8) }}</td>
                                    <td>{{ number_format((float) $order['total_amount'], 8) }}</td>
                                    <td>
                                        @if ($order['status'] === 'filled')
                                            <span class="badge bg-success-subtle text-success">{{ __('admin.filled') }}</span>
                                        @elseif ($order['status'] === 'open')
                                            <span class="badge bg-info-subtle text-info">{{ __('admin.open') }}</span>
                                        @elseif ($order['status'] === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger">{{ __('admin.cancelled') }}</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">{{ $order['status'] }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order['created_at'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/trade-chart.js'])
    @vite(['resources/js/admin/dashboard-chart.js'])
    <script>
        window.CHART_CONFIG = {
            apiBase:         '{{ url("/api/internal/chart") }}',
            futureApiBase:   '{{ url("/api/admin/future-chart") }}',
            reverbHost:      '{{ env('REVERB_HOST', '0.0.0.0') }}',
            reverbPort:      {{ (int) env("REVERB_PORT", 8080) }},
            reverbKey:       '{{ env("REVERB_APP_KEY", "") }}',
            reverbScheme:    '{{ env("REVERB_SCHEME", "http") }}',
            defaultSymbol:   '{{ collect(config("trading_chart.symbols", ["BTC_USDT"]))->first() }}',
            defaultInterval: '{{ collect(config("trading_chart.intervals", ["1m"]))->first() }}',
        };
    </script>
@endpush
