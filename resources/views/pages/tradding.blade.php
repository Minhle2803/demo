@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('messages.trading.title') }}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('landing2') }}">{{ __('messages.trading.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('messages.trading.title') }}</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    @if(1 > 2)
    <div class="col-xxl-3">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ __('messages.trading.title') }}</h4>
            </div><!-- end card-header -->
            <div class="card-body p-0">
                <ul class="list-group list-group-flush border-dashed mb-0" id="market-list">
                </ul><!-- end ul -->
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
    @endif
    <div class="col-xxl-9">
        <div class="card card-height-100">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1" >{{ __('messages.trading.market_graph') }}</h4>
                <!-- <div>
                    <button type="button" class="btn btn-soft-secondary btn-sm material-shadow-none">
                        1H
                    </button>
                    <button type="button" class="btn btn-soft-secondary btn-sm material-shadow-none">
                        7D
                    </button>
                    <button type="button" class="btn btn-soft-secondary btn-sm material-shadow-none">
                        1M
                    </button>
                    <button type="button" class="btn btn-soft-secondary btn-sm material-shadow-none">
                        1Y
                    </button>
                    <button type="button" class="btn btn-soft-primary btn-sm material-shadow-none">
                        ALL
                    </button>
                </div> -->
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
                                        <h6 class="mb-2 text-muted">{{ __('messages.trading.high') }}</h6>
                                        <h5 class="text-success mb-0" id="market-high"></h5>
                                    </div>
                                    <div class="pe-3">
                                        <h6 class="mb-2 text-muted">{{ __('messages.trading.low') }}</h6>
                                        <h5 class="text-danger mb-0" id="market-low"></h5>
                                    </div>
                                    <div>
                                        <h6 class="mb-2 text-muted">{{ __('messages.trading.market_volume') }}</h6>
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
    <!--end col-->
    <div class="col-xxl-3">
        <div class="card card-height-100">
            <div class="card-header">
                <ul class="nav nav-tabs-custom rounded card-header-tabs nav-justified border-bottom-0 mx-n3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#cryptoBuy" role="tab">
                            {{ __('messages.trading.place_order') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content text-muted">
                    <div class="tab-pane active" id="cryptoBuy" role="tabpanel">
                        <div class="p-3">
                            
                            <div class="mt-3 pt-2">
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-13 mb-0">{{ __('messages.trading.date') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0" id="date"></h6>
                                    </div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-13 mb-0">{{ __('messages.trading.hour') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0" id="time"></h6>
                                    </div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-13 mb-0">{{ __('messages.trading.current_session') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0" id="session-id"></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <select class="form-select" id="trade-amount">
                                            <option value="20000">20,000 VND</option>
                                            <option value="50000">50,000 VND</option>
                                            <option value="100000">100,000 VND</option>
                                            <option value="200000">200,000 VND</option>
                                            <option value="500000">500,000 VND</option>
                                            <option value="1000000">1,000,000 VND</option>
                                            <option value="2000000">2,000,000 VND</option>
                                            <option value="5000000">5,000,000 VND</option>
                                            <option value="10000000">10,000,000 VND</option>
                                            <option value="20000000">20,000,000 VND</option>
                                            <option value="50000000">50,000,000 VND</option>
                                            <option value="100000000">100,000,000 VND</option>
                                            <option value="200000000">200,000,000 VND</option>
                                            <option value="500000000">500,000,000 VND</option>
                                        </select>
                                    </div>
                                </div><!-- end col -->
                            </div><!-- end row -->
                            <div class="card">
                                <div class="bg-light card-body p-4 text-center">
                                    <h5 class="card-title mb-0">{{ __('messages.trading.place_order') }}: <span id="session-countdown">00:00</span></h5>
                                </div><!-- end cardbody -->
                            </div>
                            <div class="flex-wrap gap-3" >
                                <button type="button" class="btn btn-success waves-effect waves-light left" style="width: 47%; float: left;" id="buyBtn">{{ __('messages.trading.buy_coin') }} <i class="mdi mdi-trending-up align-middle me-1"></i></button>
                                <button type="button" class="btn btn-danger waves-effect waves-light right" style="width: 47%; float: right;" id="sellBtn">{{ __('messages.trading.sell_coin') }} <i class="mdi mdi-trending-down align-middle me-1"></i></button>
                            </div>
                        </div>
                    </div>
                    <!--end tab-pane-->
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="card" id="marketList">
    <div class="card-header border-bottom-dashed">
        <div class="row align-items-center">
            <div class="col-3">
                <h5 class="card-title mb-0">{{ __('messages.trading.history') }}</h5>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end card-header-->
    <div class="card-body">
        <div class="table-responsive table-card">
            <table class="table align-middle table-nowrap"  id="tradesTable">
                <thead class="table-light text-muted">
                    <tr>
                        <th>{{ __('messages.trading.symbol') }}</th>
                        <th>{{ __('messages.trading.session_id') }}</th>
                        <th>{{ __('messages.trading.type') }}</th>
                        <th>{{ __('messages.trading.open_price') }}</th>
                        <th>{{ __('messages.trading.close_price') }}</th>
                        <th>{{ __('messages.trading.status') }}</th>
                        <th>{{ __('messages.trading.amount') }}</th>
                        <th>{{ __('messages.trading.payout') }}</th>
                        <th>{{ __('messages.trading.time') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trades as $trade)
                        <tr data-id="{{ $trade->id }}">
                            <td>
                                <?php
                                    // Map session_symbol to a human-readable name and icon
                                    $coinMeta = config('trading_chart.coins', []);
                                    $tradeSymbol = $trade->session_symbol;
                                    $meta = $coinMeta[$tradeSymbol] ?? [];
                                    $displayName = $meta['name'] ?? $tradeSymbol;
                                    $iconPath = asset('assets/images/svg/crypto-icons/' . ($meta['icon'] ?? 'default').'.svg');
                                ?>
                                <div class="d-flex align-items-center fw-medium">
                                    <img src="{{ $iconPath }}" alt="" class="avatar-xxs me-2">
                                    <a href="javascript:void(0);" class="currency_name">{{ $displayName }}</a>
                                </div>
                            </td>
                            <td>{{ $trade->session_id }}</td>
                            <td>
                                <?php
                                    $textClass = "text-success";
                                    if ($trade->type === 'sell') {
                                        $textClass = "text-danger";
                                    }
                                ?>
                                <h6 class="{{ $textClass }} fs-13 mb-0">{{ $trade->type }}</h6>
                            </td>
                            <td>{{ $trade->session_open_price }}</td>
                            <td>{{ $trade->session_close_price }}</td>
                            <td>
                                <?php
                                    $textClass = "text-success";
                                    if ($trade->status === 'lose') {
                                        $textClass = "text-danger";
                                    }
                                ?>
                                <h6 class="{{ $textClass }} fs-13 mb-0">{{ $trade->status }}</h6>    
                            </td>
                            <td>{{ number_format($trade->amount, 0, '.', ',') }}</td>
                            <?php 
                                $amount = number_format($trade->amount, 0, '.', ',');
                                $textClass = "text-success";
                                $text = "+" . $amount;
                                if ($trade->status === 'lose') {
                                    $textClass = "text-danger";
                                    $text = "-". $amount;
                                } else if ($trade->status === 'pending') {
                                    $text = "";
                                }
                            ?>
                            <td>
                                <h6 class="{{ $textClass }} fs-13 mb-0">{{ $text }}</h6>  
                            </td>
                            <td>{{ $trade->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <div class="pagination-wrap hstack gap-2">
                <?php
                    // Preserve existing query parameters except 'page'
                    $disabledPrev = $trades->onFirstPage() ? 'disabled' : '';
                    $hrefPrev = $trades->onFirstPage() ? '#' : $trades->previousPageUrl();

                    $disabledNext = $trades->hasMorePages() ? '' : 'disabled';
                    $hrefNext = $trades->hasMorePages() ? $trades->nextPageUrl() : '#';
                ?>
                <a class="page-item pagination-prev {{ $disabledPrev }}" href="{{ $hrefPrev }}">
                    {{ __('messages.common.previous') }}
                </a>
                <ul class="pagination listjs-pagination mb-0"></ul>
                <a class="page-item pagination-next {{ $disabledNext }}" href="{{ $hrefNext }}">
                    {{ __('messages.common.next') }}
                </a>
            </div>
        </div>
    </div>
    <!--end card-body-->
</div>
<!--end card-->
<!-- Trade Confirmation Modal -->
<div class="modal fade" id="tradeConfirmModal" tabindex="-1" aria-labelledby="tradeConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tradeConfirmModalLabel">{{ __('messages.trading.confirm_order') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <h4 id="confirm-type" class="fw-bold"></h4>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-1">{{ __('messages.trading.symbol') }}</p>
                    <h5 id="confirm-symbol"></h5>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-1">{{ __('messages.trading.price') }}</p>
                    <h5 id="confirm-price"></h5>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-1">{{ __('messages.trading.amount') }}</p>
                    <h5 id="confirm-amount"></h5>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                <button type="button" class="btn btn-success" id="confirmTradeBtn">{{ __('messages.common.confirm') }}</button>
            </div>
        </div>
    </div>
</div>
@include('layouts.modal.modal_message')
@endsection
@push('scripts')

    {{-- Vite compiled entry --}}
    @vite(['resources/js/trade-chart.js'])
    @vite('resources/js/trading-session/main.js')
    {{--
        Inject Laravel server-side config into window.CHART_CONFIG.
        chart-demo.js reads from this object.
    --}}
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
        window.tradeTableConfig = {
            latestApiUrl: "",
            coinMeta: @json(config('trading_chart.coins', [])),
            maxRows: 20,
            interval: 3000,
        };
    </script>
@endpush