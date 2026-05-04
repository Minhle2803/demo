@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')


<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('messages.spot.title') }}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.spot.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('messages.spot.title') }}</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

{{-- ════════════════════════════════════════════════════════════════════
     ROW 1: Order Book (col-3) + Chart (col-9)
════════════════════════════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-xxl-3">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ __('messages.spot.order_book') }}</h4>
                <select class="form-select form-select-sm w-auto" id="orderBookSymbol">
                    @foreach (config('spot_trading.symbols', []) as $key => $cfg)
                        <option value="{{ $key }}">{{ $cfg['base_asset'] }}/{{ $cfg['quote_asset'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 520px;">
                    <table class="table table-sm table-borderless mb-0 orderbook-table">
                        <thead class="text-muted small">
                            <tr>
                                <th class="ps-3">{{ __('messages.spot.price_header') }}</th>
                                <th class="text-end">{{ __('messages.spot.quantity') }}</th>
                                <th class="text-end pe-3">{{ __('messages.spot.order_count') }}</th>
                            </tr>
                        </thead>
                        <tbody id="orderBookAsks">
                            <tr><td colspan="3" class="text-center text-muted py-3">{{ __('messages.common.loading') }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="border-top border-bottom py-2 px-3 text-center" id="orderBookSpread">
                    <span class="fw-bold" id="orderBookMidPrice">---</span>
                    <span class="text-muted small ms-2" id="orderBookMidSpread">---</span>
                </div>
                <div class="table-responsive" style="max-height: 520px;">
                    <table class="table table-sm table-borderless mb-0 orderbook-table">
                        <tbody id="orderBookBids">
                            <tr><td colspan="3" class="text-center text-muted py-3">{{ __('messages.common.loading') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-9">
        <div class="card card-height-100">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ __('messages.spot.market_graph') }}</h4>
            </div><!-- end card header -->
            <div class="card-body p-0">
                <div class="bg-light-subtle border-top-dashed border border-start-0 border-end-0 border-bottom-dashed py-3 px-4">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <div class="d-flex flex-wrap gap-4 align-items-center">
                                <div>
                                    <h3 class="fs-19" id="market-price"><small class="fs-14 text-muted">.00</small></h3>
                                    <p class="text-muted text-uppercase fw-medium mb-0"> <span  id="market-symbol"></span> <small class="badge bg-success-subtle text-success" id="market-bg"><i class="ri-arrow-right-up-line align-bottom" id="market-icon"></i> <span id="market-change">2.15%</span></small></p>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col-9">
                            <div class="d-flex">
                                <div class="d-flex justify-content-end text-end flex-wrap gap-4 ms-auto">
                                    <div class="pe-3">
                                        <h6 class="mb-2 text-muted">{{ __('messages.spot.high') }}</h6>
                                        <h5 class="text-success mb-0" id="market-high"></h5>
                                    </div>
                                    <div class="pe-3">
                                        <h6 class="mb-2 text-muted">{{ __('messages.spot.low') }}</h6>
                                        <h5 class="text-danger mb-0" id="market-low"></h5>
                                    </div>
                                    <div>
                                        <h6 class="mb-2 text-muted">{{ __('messages.spot.market_volume') }}</h6>
                                        <h5 class="text-danger mb-0" id="market-volume"></h5>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div>
            </div><!-- end cardbody -->
            <div class="card-body p-0 pb-3">
                <div id="chart_container" style="width: 100%; height: 500px; min-height: 500px;"></div>
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════
     ROW 2: Buy (col-6) + Sell (col-6) — two columns, no tabs
════════════════════════════════════════════════════════════════════ --}}
<div class="row mt-3" id="tradeSection">
    {{-- Buy Column --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success-subtle">
                <h5 class="card-title mb-0 text-success">{{ __('messages.spot.buy') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.spot.trading_pair') }}</label>
                    <select class="form-select" id="spotSymbol">
                        @foreach (config('spot_trading.symbols', []) as $key => $cfg)
                            <option value="{{ $key }}">{{ $cfg['base_asset'] }}/{{ $cfg['quote_asset'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.spot.price') }}</label>
                    <input type="number" class="form-control" id="buyPrice" placeholder="0.00" step="0.01" min="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.spot.quantity') }}</label>
                    <input type="number" class="form-control" id="buyQuantity" placeholder="0.00000000" step="0.00000001" min="0.00000001">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('messages.spot.total') }}: <strong id="buyTotal">0.00 USDT</strong></span>
                        <span class="text-muted small">{{ __('messages.spot.available') }}: <span id="buyAvailable">0.00</span> USDT</span>
                    </div>
                </div>
                <div id="buyError" class="alert alert-danger py-2" style="display:none;"></div>
                <button type="button" class="btn btn-success w-100" id="spotBuyBtn">{{ __('messages.spot.buy') }}</button>
            </div>
        </div>
    </div>

    {{-- Sell Column --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger-subtle">
                <h5 class="card-title mb-0 text-danger">{{ __('messages.spot.sell') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.spot.trading_pair') }}</label>
                    <select class="form-select" id="spotSellSymbol">
                        @foreach (config('spot_trading.symbols', []) as $key => $cfg)
                            <option value="{{ $key }}">{{ $cfg['base_asset'] }}/{{ $cfg['quote_asset'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.spot.price') }}</label>
                    <input type="number" class="form-control" id="sellPrice" placeholder="0.00" step="0.01" min="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.spot.quantity') }}</label>
                    <input type="number" class="form-control" id="sellQuantity" placeholder="0.00000000" step="0.00000001" min="0.00000001">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('messages.spot.total') }}: <strong id="sellTotal">0.00 USDT</strong></span>
                        <span class="text-muted small">{{ __('messages.spot.available') }}: <span id="sellAvailable">0.00</span> <span id="sellAssetLabel">BTC</span></span>
                    </div>
                </div>
                <div id="sellError" class="alert alert-danger py-2" style="display:none;"></div>
                <button type="button" class="btn btn-danger w-100" id="spotSellBtn">{{ __('messages.spot.sell') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════
     ROW 3: Open Orders + Trade History
════════════════════════════════════════════════════════════════════ --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card" id="marketList">
            <div class="card-header border-bottom-dashed">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#openOrdersTab" role="tab">{{ __('messages.spot.open_orders') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tradeHistoryTab" role="tab">{{ __('messages.spot.trade_history') }}</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="openOrdersTab" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap table-sm">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th>{{ __('messages.spot.symbol') }}</th>
                                        <th>{{ __('messages.spot.side') }}</th>
                                        <th>{{ __('messages.spot.price_header') }}</th>
                                        <th>{{ __('messages.spot.qty') }}</th>
                                        <th>{{ __('messages.spot.filled') }}</th>
                                        <th>{{ __('messages.spot.status') }}</th>
                                        <th>{{ __('messages.spot.time') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="openOrdersBody">
                                    <tr><td colspan="8" class="text-center text-muted">{{ __('messages.common.loading') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tradeHistoryTab" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap table-sm">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th>{{ __('messages.spot.symbol') }}</th>
                                        <th>{{ __('messages.spot.side') }}</th>
                                        <th>{{ __('messages.spot.price_header') }}</th>
                                        <th>{{ __('messages.spot.qty') }}</th>
                                        <th>{{ __('messages.spot.total') }}</th>
                                        <th>{{ __('messages.spot.source') }}</th>
                                        <th>{{ __('messages.spot.time') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="tradeHistoryBody">
                                    <tr><td colspan="7" class="text-center text-muted">{{ __('messages.common.loading') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
.orderbook-table {
    font-size: 13px;
}
.orderbook-table tbody tr {
    cursor: pointer;
    line-height: 1.3;
}
.orderbook-table tbody tr:hover {
    background: rgba(255,255,255,0.03);
}
.orderbook-row--ask td:first-child {
    color: #f06548 !important;
}
.orderbook-row--bid td:first-child {
    color: #0ab39c !important;
}
.orderbook-bar-wrapper {
    display: inline-block;
    position: relative;
    width: 60px;
    height: 10px;
    margin-right: 4px;
    vertical-align: middle;
}
.orderbook-bar {
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    border-radius: 2px;
    opacity: 0.25;
}
.orderbook-bar--ask {
    background-color: #f06548;
}
.orderbook-bar--bid {
    background-color: #0ab39c;
}
#orderBookSpread {
    font-size: 13px;
}
#orderBookMidPrice {
    font-size: 18px;
}
</style>
@endpush
@push('scripts')
    @vite(['resources/js/trade-chart.js'])
    @vite('resources/js/spot-trading/main.js')

    <script>
        window.CHART_CONFIG = {
            apiBase:         '{{ url("/api/internal/chart") }}',
            reverbHost:      '{{ env('REVERB_HOST', '0.0.0.0') }}',
            reverbPort:      {{ (int) env("REVERB_PORT", 8080) }},
            reverbKey:       '{{ env("REVERB_APP_KEY", "") }}',
            reverbScheme:    '{{ env("REVERB_SCHEME", "http") }}',
            defaultSymbol:   '{{ collect(config("trading_chart.symbols", ["BTC_USDT"]))->first() }}',
            defaultInterval: '{{ collect(config("trading_chart.intervals", ["1m"]))->first() }}',
        };
        window.SPOT_CONFIG = {
            baseUrl: '{{ url("/api/spot") }}',
            symbols: @json(config('spot_trading.symbols')),
            reverbHost:      '{{ env('REVERB_HOST', '0.0.0.0') }}',
            reverbPort:      {{ (int) env("REVERB_PORT", 8080) }},
            reverbKey:       '{{ env("REVERB_APP_KEY", "") }}',
            reverbScheme:    '{{ env("REVERB_SCHEME", "http") }}',
        };
        window.__currentPrice = null;
    </script>
@endpush
