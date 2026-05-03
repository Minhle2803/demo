@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Giao dịch Spot</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Giao dịch Spot</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-xxl-3">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Giao dịch Spot</h4>
            </div><!-- end card-header -->
            <div class="card-body p-0">
                <ul class="list-group list-group-flush border-dashed mb-0" id="market-list">
                </ul><!-- end ul -->
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xxl-6">
        <div class="card card-height-100">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1" >Market Graph</h4>
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
                                        <h6 class="mb-2 text-muted">High</h6>
                                        <h5 class="text-success mb-0" id="market-high"></h5>
                                    </div>
                                    <div class="pe-3">
                                        <h6 class="mb-2 text-muted">Low</h6>
                                        <h5 class="text-danger mb-0" id="market-low"></h5>
                                    </div>
                                    <div>
                                        <h6 class="mb-2 text-muted">Market Volume</h6>
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
    <div class="col-xxl-3" id="tradeSection">
        <div class="card card-height-100">
            <div class="card-header">
                <ul class="nav nav-tabs-custom rounded card-header-tabs nav-justified border-bottom-0 mx-n3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#cryptoBuy" role="tab">
                            Giao dịch
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
                                        <p class="fs-13 mb-0">Ngày</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0" id="date"></h6>
                                    </div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-13 mb-0">Giờ</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0" id="time"></h6>
                                    </div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="flex-grow-1">
                                        <p class="fs-13 mb-0">Phiên hiện tại</p>
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
                                    <h5 class="card-title mb-0">Hãy đặt lệnh: <span id="session-countdown">00:00</span></h5>
                                </div><!-- end cardbody -->
                            </div>
                            <div class="flex-wrap gap-3" >
                                <button type="button" class="btn btn-success waves-effect waves-light left" style="width: 47%; float: left;" id="buyBtn">Buy Coin <i class="mdi mdi-trending-up align-middle me-1"></i></button>
                                <button type="button" class="btn btn-danger waves-effect waves-light right" style="width: 47%; float: right;" id="sellBtn">Sell Coin <i class="mdi mdi-trending-down align-middle me-1"></i></button>
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
                <h5 class="card-title mb-0">History</h5>
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
                        <th>Symbol</th>
                        <th>Session ID</th>
                        <th>type</th>
                        <th>Open price</th>
                        <th>Close Price</th>
                        <th>status</th>
                        <th>Amount</th>
                        <th>payout</th>
                        <th>Time</th>
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
                                $iconPath = asset('assets/images/svg/crypto-icons/'.($meta['icon'] ?? 'default').'.svg');
                                ?>
                                <div class="d-flex align-items-center fw-medium">
                                    <img src="{{ $iconPath }}" alt="" class="avatar-xxs me-2">
                                    <a href="javascript:void(0);" class="currency_name">{{ $displayName }}</a>
                                </div>
                            </td>
                            <td>{{ $trade->session_id }}</td>
                            <td>
                                <?php
                                    $textClass = 'text-success';
                                if ($trade->type === 'sell') {
                                    $textClass = 'text-danger';
                                }
                                ?>
                                <h6 class="{{ $textClass }} fs-13 mb-0">{{ $trade->type }}</h6>
                            </td>
                            <td>{{ $trade->session_open_price }}</td>
                            <td>{{ $trade->session_close_price }}</td>
                            <td>
                                <?php
                                    $textClass = 'text-success';
                                if ($trade->status === 'lose') {
                                    $textClass = 'text-danger';
                                }
                                ?>
                                <h6 class="{{ $textClass }} fs-13 mb-0">{{ $trade->status }}</h6>    
                            </td>
                            <td>{{ number_format($trade->amount, 0, '.', ',') }}</td>
                            <td>{{ number_format($trade->payout, 0, '.', ',') }}</td>
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
                    Previous
                </a>
                <ul class="pagination listjs-pagination mb-0"></ul>
                <a class="page-item pagination-next {{ $disabledNext }}" href="{{ $hrefNext }}">
                    Next
                </a>
            </div>
        </div>
    </div>
    <!--end card-body-->
</div>
<!--end card-->
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
            reverbHost:      'localhost',
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