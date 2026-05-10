<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a class="logo logo-dark" href="{{ route('landing2') }}">
                        <span class="logo-sm">
                            <img alt="" height="22" src="{{ asset('assets/images/logo/logo.png') }}" />
                        </span>
                        <span class="logo-lg">
                            <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
                        </span>
                    </a>
                    <a class="logo logo-light" href="{{ route('landing2') }}">
                        <span class="logo-sm">
                            <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
                        </span>
                        <span class="logo-lg">
                            <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
                        </span>
                    </a>
                </div>
                <button class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon" type="button">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input autocomplete="off" class="form-control" id="search-options" placeholder="{{ __('messages.common.search') }}" type="text" value="" />
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar="" style="max-height: 320px;">
                            <!-- item-->
                            <div class="dropdown-header">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Recent Searches</h6>
                            </div>
                            <div class="dropdown-item bg-transparent text-wrap">
                                <a class="btn btn-soft-secondary btn-sm rounded-pill" href="{{ route('landing2') }}">how to setup <i class="mdi mdi-magnify ms-1"></i></a>
                                <a class="btn btn-soft-secondary btn-sm rounded-pill" href="{{ route('landing2') }}">buttons <i class="mdi mdi-magnify ms-1"></i></a>
                            </div>
                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                            </div>
                            <!-- item-->
                            <a class="dropdown-item notify-item" href="javascript:void(0);">
                                <i class="ri-bubble-chart-line align-middle fs-18 text-muted me-2"></i>
                                <span>Analytics Dashboard</span>
                            </a>
                            <!-- item-->
                            <a class="dropdown-item notify-item" href="javascript:void(0);">
                                <i class="ri-lifebuoy-line align-middle fs-18 text-muted me-2"></i>
                                <span>Help Center</span>
                            </a>
                            <!-- item-->
                            <a class="dropdown-item notify-item" href="javascript:void(0);">
                                <i class="ri-user-settings-line align-middle fs-18 text-muted me-2"></i>
                                <span>My account settings</span>
                            </a>
                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-2 text-uppercase">Members</h6>
                            </div>
                            <div class="notification-list">
                                <!-- item -->
                                <a class="dropdown-item notify-item py-2" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-2.jpg') }}" />
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Angela Bernier</h6>
                                            <span class="fs-11 mb-0 text-muted">Manager</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a class="dropdown-item notify-item py-2" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-3.jpg') }}" />
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">David Grasso</h6>
                                            <span class="fs-11 mb-0 text-muted">Web Designer</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a class="dropdown-item notify-item py-2" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-5.jpg') }}" />
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Mike Bunch</h6>
                                            <span class="fs-11 mb-0 text-muted">React Developer</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="text-center pt-3 pb-1">
                            <a class="btn btn-primary btn-sm" href="pages-search-results.html">View All Results <i class="ri-arrow-right-line ms-1"></i></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button aria-expanded="false" aria-haspopup="true" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" id="page-header-search-dropdown" type="button">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div aria-labelledby="page-header-search-dropdown" class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input aria-label=') }}"Recipient' s username" class="form-control" placeholder="Search ..." type="text" />
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button aria-expanded="false" aria-haspopup="true" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" type="button">
                        <img alt="Header Language" class="rounded" height="20" id="header-lang-img" src="{{ asset('assets/images/flags/' . (app()->getLocale() === 'vi' ? 'vn' : 'us') . '.svg') }}" />
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item notify-item language py-2 {{ app()->getLocale() === 'en' ? 'active' : '' }}" data-lang="en" href="{{ route('lang.switch', 'en') }}" title="English">
                            <img alt="EN" class="me-2 rounded" height="18" src="{{ asset('assets/images/flags/us.svg') }}" />
                            <span class="align-middle">English</span>
                        </a>
                        <a class="dropdown-item notify-item language py-2 {{ app()->getLocale() === 'vi' ? 'active' : '' }}" data-lang="vi" href="{{ route('lang.switch', 'vi') }}" title="Vietnamese">
                            <img alt="VI" class="me-2 rounded" height="18" src="{{ asset('assets/images/flags/vn.svg') }}" />
                            <span class="align-middle">Tiếng Việt</span>
                        </a>
                    </div>
                </div>
                @auth('client')
                
                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <a class="btn btn-outline-warning btn-label waves-effect waves-light" href="{{ route('client.profile.show') }}"><i class="bx bx-money label-icon align-middle fs-16 me-2"></i> Nạp Tiền</a>
                </div>
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button aria-expanded="false" aria-haspopup="true" class="btn material-shadow-none" data-bs-toggle="dropdown" id="page-header-user-dropdown" type="button">
                        <span class="d-flex align-items-center">
                            <img alt="Header Avatar" class="rounded-circle header-profile-user" src="{{ asset('assets/images/users/avatar-1.jpg') }}" />
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::guard('client')->user()->nickname }}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ Auth::guard('client')->user()->balance }} VND</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">{{ __('messages.auth.welcome_user', ['name' => Auth::guard('client')->user()->nickname]) }}</h6>
                        <a class="dropdown-item" href="{{ route('client.profile.show') }}"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.profile') }}</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('client.profile.show') }}"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.balance') }} : <b>{{ Auth::guard('client')->user()->balance }}</b></span></a>
                        <a class="dropdown-item" href="{{ route('client.logout') }}"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.logout') }}</span></a>
                    </div>
                </div>
                @endauth
                @guest('client')
                <div class="dropdown ms-sm-3 ">
                    <a href="{{ route('signin') }}" style="border-color: white; color: white;" class="btn btn-light waves-effect me-1 rounded-1">{{ __('messages.auth.sign_in') }}</a>
                    <a href="{{ route('signup') }}" class="btn btn-warning waves-effect waves-light">{{ __('messages.auth.sign_up') }}</a>
                </div>
                @endguest
            </div>
        </div>
    </div>
</header>