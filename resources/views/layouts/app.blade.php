<!doctype html>
<html lang="en" data-bs-theme="dark" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="sm-hover" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default" love-deals="879BC0364EB9EBEE3DBE71B15E175613" data-layout-width="fluid" data-layout-position="fixed" data-layout-style="default" data-body-image="none" data-sidebar-visibility="show">
<head>
    <meta charset="utf-8" />
    <title>Binance: Sàn giao dịch tiền điện tử đáng tin cậy nhất thế giới để mua, giao dịch và đầu tư vào tiền điện tử.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    @include('layouts.partials.head')
    @stack('styles')
    
</head>
<body>
    <div id="layout-wrapper">
        @include('layouts.partials.topbar')
        @include('layouts.partials.modal-notifications')
        @include('layouts.partials.sidebar')
        @include('layouts.partials.overlay')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            @include('layouts.partials.footer')
        </div>
    </div>

    @include('layouts.partials.back-to-top')
    @include('layouts.partials.preloader')
    @include('layouts.partials.customizer')
    @include('layouts.partials.scripts')
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
