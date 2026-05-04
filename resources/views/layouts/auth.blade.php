<!doctype html>
<html lang="en" data-bs-theme="dark" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="sm-hover" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default" love-deals="879BC0364EB9EBEE3DBE71B15E175613" data-layout-width="fluid" data-layout-position="fixed" data-layout-style="default" data-body-image="none" data-sidebar-visibility="show">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') | Binance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    @include('layouts.partials.head-auth')
    @stack('styles')
    
</head>
<body>
    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                    @yield('content')
            </div>
        </div>

        @include('layouts.partials.footer')
    </div>

    @include('layouts.partials.scripts-auth')
    @stack('scripts')
</body>
</html>
