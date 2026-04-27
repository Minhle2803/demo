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
                <div>
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
                </div>
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
    <div class="col-xxl-3">
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
                <h5 class="card-title mb-0">Markets</h5>
            </div>
            <!--end col-->
            <div class="col-auto ms-auto">
                <div class="d-flex gap-2">
                    <button class="btn btn-success"><i class="ri-equalizer-line align-bottom me-1"></i> Filters</button>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end card-header-->
    <div class="card-body p-0 border-bottom border-bottom-dashed">
        <div class="search-box">
            <input type="text" class="form-control search border-0 py-3" placeholder="Search to currency...">
            <i class="ri-search-line search-icon"></i>
        </div>
    </div>
    <!--end card-body-->
    <div class="card-body">
        <div class="table-responsive table-card">
            <table class="table align-middle table-nowrap" id="customerTable">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="sort" data-sort="currency_name" scope="col">Currency</th>
                        <th class="sort" data-sort="current_value" scope="col">Price</th>
                        <th class="sort" data-sort="pairs" scope="col">Pairs</th>
                        <th class="sort" data-sort="high" scope="col">24 High</th>
                        <th class="sort" data-sort="low" scope="col">24 Low</th>
                        <th class="sort" data-sort="market_cap" scope="col">Market Volume</th>
                        <th class="sort" data-sort="valume" scope="col">Volume %</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <!--end thead-->
                <tbody class="list form-check-all">
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ001</a></td>
                        <td>
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/btc.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Bitcoin (BTC)</a>
                            </div>
                        </td>
                        <td class="current_value">$47,071.60</td>
                        <td class="pairs">BTC/USD</td>
                        <td class="high">$28,722.76</td>
                        <td class="low">$68,789.63</td>
                        <td class="market_cap">$888,411,910</td>
                        <td class="valume">
                            <h6 class="text-success fs-13 mb-0"><i class="mdi mdi-trending-up align-middle me-1"></i>1.50%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ002</a></td>
                        <td>
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/eth.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Ethereum (ETH)</a>
                            </div>
                        </td>
                        <td class="current_value">$3,813.14</td>
                        <td class="pairs">ETH/USDT</td>
                        <td class="high">$4,036.24</td>
                        <td class="low">$3,588.14</td>
                        <td class="market_cap">$314,520,675</td>
                        <td class="valume">
                            <h6 class="text-danger fs-13 mb-0"><i class="mdi mdi-trending-down align-middle me-1"></i>0.42%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ003</a></td>
                        <td>
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/ltc.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Litecoin (LTC)</a>
                            </div>
                        </td>
                        <td class="current_value">$149.65</td>
                        <td class="pairs">LTC/USDT</td>
                        <td class="high">$412.96</td>
                        <td class="low">$104.33</td>
                        <td class="market_cap">$314,520,675</td>
                        <td class="valume">
                            <h6 class="text-success fs-13 mb-0"><i class="mdi mdi-trending-up align-middle me-1"></i>0.89%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ004</a></td>
                        <td>
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/xmr.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Monero (XMR)</a>
                            </div>
                        </td>
                        <td class="current_value">$17,491.16</td>
                        <td class="pairs">XRM/USDT</td>
                        <td class="high">$31,578.35</td>
                        <td class="low">$8691.75</td>
                        <td class="market_cap">$9,847,327</td>
                        <td class="valume">
                            <h6 class="text-success fs-13 mb-0"><i class="mdi mdi-trending-up align-middle me-1"></i>1.92%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ005</a></td>
                        <td>
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/sol.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Solana (SOL)</a>
                            </div>
                        </td>
                        <td class="current_value">$172.93</td>
                        <td class="pairs">SOL/USD</td>
                        <td class="high">$178.37</td>
                        <td class="low">$172.3</td>
                        <td class="market_cap">$40,559,274</td>
                        <td class="valume">
                            <h6 class="text-danger fs-13 mb-0"><i class="mdi mdi-trending-down align-middle me-1"></i>2.87%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ006</a></td>
                        <td class="currency_name">
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/ant.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Aragon (ANT)</a>
                            </div>
                        </td>
                        <td class="current_value">$13.31</td>
                        <td class="pairs">ANT/USD</td>
                        <td class="high">$13.85</td>
                        <td class="low">$12.53</td>
                        <td class="market_cap">$156,209,195.18</td>
                        <td class="valume">
                            <h6 class="text-success fs-13 mb-0"><i class="mdi mdi-trending-up align-middle me-1"></i>3.96%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ007</a></td>
                        <td>
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/fil.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Filecoin (FIL)</a>
                            </div>
                        </td>
                        <td class="current_value">$35.21</td>
                        <td class="pairs">FIL/USD</td>
                        <td class="high">$36.41</td>
                        <td class="low">$35.03</td>
                        <td class="market_cap">$374,618,945.51</td>
                        <td class="valume">
                            <h6 class="text-danger fs-13 mb-0"><i class="mdi mdi-trending-down align-middle me-1"></i>0.84%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ008</a></td>
                        <td>
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/aave.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Aave (AAVE)</a>
                            </div>
                        </td>
                        <td class="current_value">$275.47</td>
                        <td class="pairs">AAVE/USDT</td>
                        <td class="high">$277.11</td>
                        <td class="low">$255.01</td>
                        <td class="market_cap">$156,209,195.18</td>
                        <td class="valume">
                            <h6 class="text-success fs-13 mb-0"><i class="mdi mdi-trending-up align-middle me-1"></i>8.20%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ009</a></td>
                        <td class="currency_name">
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/ada.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Cardano (ADA)</a>
                            </div>
                        </td>
                        <td class="current_value">$1.35</td>
                        <td class="pairs">ADA/USD</td>
                        <td class="high">$1.39</td>
                        <td class="low">$1.32</td>
                        <td class="market_cap">$880,387,980.14</td>
                        <td class="valume">
                            <h6 class="text-danger fs-13 mb-0"><i class="mdi mdi-trending-down align-middle me-1"></i>0.42%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                    <tr>
                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary">#VZ010</a></td>
                        <td class="currency_name">
                            <div class="d-flex align-items-center fw-medium">
                                <img src="assets/images/svg/crypto-icons/dot.svg" alt="" class="avatar-xxs me-2">
                                <a href="javascript:void(0);" class="currency_name">Polkadot (DOT)</a>
                            </div>
                        </td>
                        <td class="current_value">$28.88</td>
                        <td class="pairs">DOT/USD</td>
                        <td class="high">$30.56</td>
                        <td class="low">$28.66</td>
                        <td class="market_cap">$880,387,980.14</td>
                        <td class="valume">
                            <h6 class="text-success fs-13 mb-0"><i class="mdi mdi-trending-up align-middle me-1"></i>1.03%</h6>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-soft-info material-shadow-none">Trade Now</button>
                        </td>
                    </tr>
                    <!--end tr-->
                </tbody>
            </table>
            <!--end table-->
            <div class="noresult" style="display: none">
                <div class="text-center">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"></lord-icon>
                    <h5 class="mt-2">Sorry! No Result Found</h5>
                    <p class="text-muted mb-0">We've searched more than 150+ Currencies We did not find any Currencies for you search.</p>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <div class="pagination-wrap hstack gap-2">
                <a class="page-item pagination-prev disabled" href="#">
                    Previous
                </a>
                <ul class="pagination listjs-pagination mb-0"></ul>
                <a class="page-item pagination-next" href="#">
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
    </script>
@endpush